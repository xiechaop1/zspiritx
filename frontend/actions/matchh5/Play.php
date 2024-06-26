<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\matchh5;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryRank;
use common\models\User;
use common\models\UserLottery;
use common\models\UserModels;
use common\models\UserPrize;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Play extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $matchName = !empty($_GET['match_name']) ? $_GET['match_name'] : '中国勒芒';

//        $userModelId = !empty($_GET['user_model_id']) ? $_GET['user_model_id'] : 0;


        $storyMatch = StoryMatch::find()
            ->where([
                'story_id' => $storyId,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'match_name' => $matchName,
                'story_match_status' => StoryMatch::STORY_MATCH_STATUS_PREPARE,
//                'user_model_id' => $userModelId,
            ])
            ->one();

        if (empty($storyMatch)) {
            return $this->renderErr('您没有准备参赛的赛车，请您用钥匙启动准备好以后，联系小精灵！');
//            throw new Exception('您没有准备参赛的赛车，请您用钥匙启动准备好以后，联系小精灵！', ErrorCode::STORY_MATCH_NOT_EXIST_READY);
        }

        $userModelId = $storyMatch->user_model_id;
        $userModel = UserModels::find()
            ->where(['id' => $userModelId])
            ->one();

        $tmpUserModelProp = !empty($userModel->user_model_prop) ? json_decode($userModel->user_model_prop, true) : [];

        if (empty($tmpUserModelProp)) {
            return $this->renderErr('您的赛车没有准备好，请联系小精灵！');
//            throw new Exception('您的赛车没有准备好，请联系小精灵！', ErrorCode::STORY_MATCH_NOT_EXIST_READY);
        }

        $userModelProp = !empty($tmpUserModelProp['prop']) ? $tmpUserModelProp['prop'] : [];
        $speed = !empty($userModelProp['speed']) ? $userModelProp['speed'] : 0;
        $cornerSpeed = !empty($userModelProp['corner_speed']) ? $userModelProp['corner_speed'] : 0;
        $acceleration = !empty($userModelProp['acc']) ? $userModelProp['acc'] : 0;
        $operate = !empty($userModelProp['operate']) ? $userModelProp['operate'] : 0;
        $reliability = !empty($userModelProp['reliability']) ? $userModelProp['reliability'] : 9800;

        $timeRate = ($speed / 350) * 0.3 + ($cornerSpeed / 300) * 0.15 + ($acceleration / 100) * 0.25 + ($operate / 100) * 0.3;
        $timeRate = $timeRate > 1 ? 1 : $timeRate;

        if (empty($timeRate)) {
            return $this->renderErr('您的赛车参数异常，请联系小精灵！');
//            throw new Exception('您的赛车参数异常，请联系小精灵！', ErrorCode::STORY_MATCH_NOT_EXIST_READY);
        }

        // 计算"中国勒芒"圈速
        $eachLopSecMax = 230000;
//        $eachLopSec = rand($eachLopSecMin, $eachLopSecMax);

        $i = 0;
        $ct = 0;
        $bestSec = 0;
        $bestCt = 0;
        $maxSpeed = 0;
        $matchDetail = [];
        $matchFlow = [];
        while ($i < 86400000) {
            if ($ct<260) {
                // 前260圈不会出现最快圈速
                // 所以圈速时间下限高一点
                $eachLopSecMin = 190000;
            } else {
                $eachLopSecMin = 178000;
            }
            $eachLopSec = rand($eachLopSecMin, $eachLopSecMax);
            $eachLopSec /= $timeRate;
            $eachLopSec = floor($eachLopSec);
            $i += $eachLopSec;
            $ct++;

            $minSpeed = round($speed / $timeRate);
            $rndMaxSpeed = $speed;

            $nSpeed = rand($minSpeed, $rndMaxSpeed);
            if ($nSpeed > $maxSpeed) {
                if ($maxSpeed > 0) {
                    $matchDetail[] = [
                        'txt' => '第' . $ct . '圈跑出<span style="color:yellow">最高时速' . $maxSpeed . '公里/小时</span>',
                        'ct' => $ct,
                        'icon' => '🚀',
                    ];
                }
                $maxSpeed = $nSpeed;
            }

            $badRand = rand(0,9000);
            if ($badRand > $reliability) {
                $badSec = rand(1000, 80000);
                $i += $badSec;
                $eachLopSec += $badSec;
                $badSecArr = \common\helpers\Common::formatTime($badSec, 'i:s.ms');
                $badSecStr = $badSecArr['str'];
                $matchDetail[] = [
                    'ct' => $ct,
                    'txt' => '第' . $ct . '圈车况出现问题，<span style="color:#ff6699">耽误了' . $badSecStr . '秒</span>',
                    'icon' => '🔧',
                ];
            }

            if ($eachLopSec < $bestSec || $bestSec == 0) {
                if ($bestSec > 0) {
                    $eachLopSecArr = \common\helpers\Common::formatTime($eachLopSec, 'i:s.ms');
                    $eachLopSecStr = $eachLopSecArr['str'];
                    $matchDetail[] = [
                        'ct' => $ct,
                        'txt' => '第' . $ct . '圈刷新了自己<span style="color:yellow">最快圈速' . $eachLopSecStr . '秒</span>',
                        'icon' => '🏁',
                    ];
                }
                $bestSec = $eachLopSec;
                $bestCt = $ct;

                $top10Rand = rand(0,100);
                if ($top10Rand > 98) {
                    $matchDetail[] = [
                        'ct' => $ct,
                        'txt' => '第' . $ct . '圈进入了前10',
                        'icon' => '🏎',
                    ];
                }
            } else {
                $top10Rand = rand(0, 100);
                if ($top10Rand > 98) {
                    $matchDetail[] = [
                        'ct' => $ct,
                        'txt' => '第' . $ct . '圈进入了前10',
                        'icon' => '🏎',
                    ];
                }
            }

            $eachLopSecStr = \common\helpers\Common::formatTimeToStr($eachLopSec, 'i:s.ms');
            $totalSecStr = \common\helpers\Common::formatTimeToStr($i);

            $matchFlow[] = [
                'ct' => $ct,
                'txt' => '第' . $ct . '圈' . $eachLopSecStr . '秒，总用时 ' . $totalSecStr . '，最高时速 ' . $nSpeed . '公里/小时',
            ];
        }

        $timeArray = \common\helpers\Common::formatTime($i);
        $bestSecArr = \common\helpers\Common::formatTime($bestSec, 'i:s.ms');

//        var_dump($ct);
//        var_dump($timeArray['str']);
//        var_dump($bestSecArr['str']);
//        var_dump($bestCt);
//        var_dump($maxSpeed);
//        var_dump($matchDetail);

        $matchDetail = [
            'flow' => $matchDetail,
            'lops' => $ct,
            'total_time' => $timeArray['str'],
            'best_time' => $bestSecArr['str'],
            'best_time_lop' => $bestCt,
            'max_speed' => $maxSpeed,
        ];

        // 保存比赛状态
        $storyMatch->match_detail = json_encode($matchDetail, true);
        $storyMatch->score = $ct;
        $storyMatch->score2 = $i;
        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
        $storyMatch->save();

        $rankRet = StoryMatch::find()
            ->where([
                'story_id' => $storyId,
//                'user_id' => $userId,
//                'session_id' => $sessionId,
                'match_name' => $matchName,
                'story_match_status' => StoryMatch::STORY_MATCH_STATUS_END
            ])
            ->andFilterWhere([
                '>=', 'score', $ct
            ])
            ->orderBy([
                'score' => SORT_DESC,
                'score2' => SORT_ASC,
            ])
            ->all();

        $rank = 0;
        if (!empty($rankRet)) {
            foreach ($rankRet as $r) {
                $rank++;
                if (
                    $r->id == $storyMatch->id
                ) {
                    break;
                }
            }
        } else {
            $rank = 1;
        }

//        $matchPrize = 10000;
        if ($rank == 1) {
            $matchPrize = 500000;
        } elseif ($rank == 2) {
            $matchPrize = 200000;
        } elseif ($rank == 3) {
            $matchPrize = 100000;
        } elseif ($rank <= 10) {
            $matchPrize = 50000;
        } else {
            $matchPrize = 10000;
        }

        if (!empty($tmpUserModelProp['match']['rank_list'])) {
            $tmpUserModelProp['match']['rank_list'][] = $rank;
        }
        if (!empty($tmpUserModelProp['match']['valuable'])) {
            $tmpUserModelProp['match']['valuable'] += $matchPrize;
        } else {
            $tmpUserModelProp['match']['valuable'] = $matchPrize;
        }
        $userModel->user_model_prop = json_encode($tmpUserModelProp, true);
        $userModel->save();

        // Todo:插入排行榜数据（调用排行service）
        // 带开发
        $storyModelId = !empty($userModel->storyModel->id) ? $userModel->storyModel->id : 0;
        $storyModelDetailId = !empty($userModel->storyModelDetail->id) ? $userModel->storyModelDetail->id : 0;

        try {
            Yii::$app->storyRank->add(
                $userId, $storyId, $sessionId,
                StoryRank::STORY_RANK_CLASS_CAR_MATCH,
                $ct, StoryRank::STORY_RANK_SORT_DESC,
                $i, StoryRank::STORY_RANK_SORT_ASC,
                $storyModelId, $storyModelDetailId, $userModelId
            );

            if (!empty($tmpUserModelProp['prop']['valuable'])) {
                $propValuable = $tmpUserModelProp['prop']['valuable'];
            } else {
                $propValuable = 0;
            }

            if (!empty($tmpUserModelProp['match']['valuable'])) {
                $matchValuable = $tmpUserModelProp['match']['valuable'];
            } else {
                $matchValuable = 0;
            }

            $totalValuable = $propValuable + $matchValuable;

            if (!empty($tmpUserModelProp['prop']['score'])) {
                $score = $tmpUserModelProp['prop']['score'];
            } else {
                $score = 0;
            }

            Yii::$app->storyRank->add(
                $userId, $storyId, $sessionId,
                StoryRank::STORY_RANK_CLASS_CAR_VALUABLE,
                $totalValuable, StoryRank::STORY_RANK_SORT_DESC,
                0, StoryRank::STORY_RANK_SORT_DESC,
                $storyModelId, $storyModelDetailId, $userModelId
            );


            Yii::$app->storyRank->add(
                $userId, $storyId, $sessionId,
                StoryRank::STORY_RANK_CLASS_CAR_SCORE,
                $score, StoryRank::STORY_RANK_SORT_DESC,
                0, StoryRank::STORY_RANK_SORT_DESC,
                $storyModelId, $storyModelDetailId, $userModelId
            );
        } catch (Exception $e) {
//            return $this->renderErr('排行榜数据插入失败，请联系小精灵！');
        }



        $ctArr = \common\helpers\Common::formatTime($i);
        $ctTime = $ctArr['str'];
        $matchDetail['flow'][] = [
            'ct' => '999',
            'txt' => '<span style="color: yellow">你用时' . $ctTime
                . '跑了' . $ct . '圈，最终获得第' . $rank . '名！</span>',
        ];

        return $this->controller->render('play', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'userModelId'   => $userModelId,
            'matchDetail'   => $matchDetail,
            'matchAllFlow' => $matchFlow,
            'matchAllFlowJson' => json_encode($matchFlow, true),
            'storyMatch'   => $storyMatch,
            'rank'  => $rank,
            'ct'    => $ct,

        ]);
    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}