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
use common\helpers\Model;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryModels;
use common\models\User;
use common\models\UserExtends;
use common\models\UserLottery;
use common\models\UserModelLoc;
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

class KnockoutPrepare extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $matchType = !empty($_GET['match_type']) ? $_GET['match_type'] : StoryMatch::MATCH_TYPE_KNOCKOUT;
        $matchName = !empty($_GET['match_name']) ? $_GET['match_name'] : '';
        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;
        $matchClass = !empty($_GET['match_class']) ? $_GET['match_class'] : 0;

        $maxPlayersCt = !empty($_GET['max_players_ct']) ? $_GET['max_players_ct'] : 30;
        $joinExpireTime = !empty($_GET['join_expire_time']) ? $_GET['join_expire_time'] : 180; // (s)

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

        $fee = 200;
        $gold = intval($fee / 5);


        $answerType = !empty($_GET['answer_type']) ? $_GET['answer_type'] : 0;

        if (empty($userId)
//            || empty($storyId)
        ) {
//            throw new Exception('参数错误', ErrorCode::PARAMS_ERROR);
            return $this->renderErr('参数错误');
        }

        $userInfo = User::find()
            ->where(['id' => $userId])
            ->one();

        $userExtends = UserExtends::find()
            ->where(['user_id' => $userId])
            ->one();

        if (empty($matchId) && empty($matchName)) {
            $matchName = date('Y-m-d H:i:s') . ' ' . $userInfo->user_name . '发起淘汰赛';
        }

        if (empty($matchId)) {
            $storyMatchPlayers = StoryMatchPlayer::find()
                ->where([
                    'user_id' => $userId,
//                    'match_type' => $matchType,
                    'match_player_status' => [
                        StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE,
                        StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
                        StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
                    ],
                ])
                ->orderBy('id desc')
                ->all();

            $matchIds = [];
            if (!empty($storyMatchPlayers)) {
                foreach ($storyMatchPlayers as $storyMatchPlayer) {
                    $matchIds[] = $storyMatchPlayer->match_id;
                }
            }
        }

        if (!empty($matchIds)) {
            $storyMatch = StoryMatch::find()
                ->where([
                    'id' => $matchIds,
                    'match_type' => $matchType,
//                    'user_id' => $userId,
//                    'session_id' => $sessionId,
//                    'story_id' => $storyId,
                    'story_match_status' => [
                        StoryMatch::STORY_MATCH_STATUS_PREPARE,
                        StoryMatch::STORY_MATCH_STATUS_MATCHING,
                        StoryMatch::STORY_MATCH_STATUS_PLAYING,
                    ],
                ])
                ->one();
            if (!empty($storyMatch)) {
                $matchId = $storyMatch->id;
            }

//            if (empty($storyMatch)) {
////                throw new Exception('对战不存在', ErrorCode::STORY_MATCH_NOT_READY);
//                return $this->renderErr('对战不存在');
//            }
        }

        if (empty($matchId)) {

            $storyMatch = StoryMatch::find()
                ->where([
                    'match_type' => $matchType,
                    'match_class' => $matchClass,
//                    'session_id' => $sessionId,
                    'story_id' => $storyId,
                    'story_match_status' => [
                        StoryMatch::STORY_MATCH_STATUS_PREPARE,
                        StoryMatch::STORY_MATCH_STATUS_MATCHING,
//                            StoryMatch::STORY_MATCH_STATUS_PLAYING,
                    ],
                ])
                ->orderBy('id desc')
                ->one();
            $matchId = !empty($storyMatch->id) ? $storyMatch->id : 0;
        }


            // 如果比赛开始，但是玩家已经结束，那么重新建立比赛
//            if (!empty($matchId)) {
//                $userPlayer = StoryMatchPlayer::find()
//                    ->where([
//                        'match_id' => $matchId,
//                        'user_id' => $userId,
//                    ])
//                    ->one();
//
//                if (!empty($userPlayer)
//                    && !in_array(
//                        $userPlayer->match_player_status, [
//                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE,
//                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
//                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
//                    ])
//                ) {
//                    $matchId = 0;
//                }
//
//            }

            if (empty($matchId)) {
                if (empty($matchClass)) {
                    $matchClassId = array_rand(StoryMatch::$matchClassRandList);
                    $matchClass = StoryMatch::$matchClassRandList[$matchClassId];
                } else {
                    if (strpos($matchClass, ',') !== false) {
                        $matchClassArray = explode(',', $matchClass);
                        $matchClassId = array_rand($matchClassArray);
                        $matchClass = $matchClassArray[$matchClassId];
                    }
                }

                $storyMatchProp = [];
                if (!empty($answerType)) {
                    $storyMatchProp['answer_type'] = $answerType;
                }

                // 出题
                $subjects = [];
                switch ($matchClass) {
                    case StoryMatch::MATCH_CLASS_MATH:
                        $ct = 2;
                        for ($level = 1; $level <= 4; $level++) {
                            $subjects = array_merge($subjects, $this->generateMathWithCt($ct, $level, $gold));
                        }
                }
                $storyMatchProp['subjects'] = $subjects;

                $storyMatch = new StoryMatch();
                $storyMatch->story_id = $storyId;
                $storyMatch->user_id = $userId;
                $storyMatch->session_id = $sessionId;
                $storyMatch->match_name = $matchName;
                $storyMatch->match_type = $matchType;
                $storyMatch->match_class = $matchClass;
                $storyMatch->story_match_prop = json_encode($storyMatchProp, JSON_UNESCAPED_UNICODE);
                $storyMatch->max_players_ct = $maxPlayersCt;
                $storyMatch->join_expire_time = time() + $joinExpireTime;
                $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_MATCHING;
                $storyMatch->save();
                $matchId = Yii::$app->db->getLastInsertID();
            }


        $storyMatchPlayer = StoryMatchPlayer::find()
            ->where([
                'match_id' => $matchId,
                'user_id' => $userId,
                'match_player_status' => [
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE,
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
                ]
            ])
            ->one();

        if (empty($storyMatchPlayer)) {
            $playerProp = [
                'grade' => !empty($userExtends->grade) ? $userExtends->grade : 1,
                'level' => !empty($userExtends->level) ? $userExtends->level : 1,
            ];
            $storyMatchPlayer = new StoryMatchPlayer();
            $storyMatchPlayer->user_id = $userId;
            $storyMatchPlayer->team_id = 1;
            $storyMatchPlayer->match_id = $matchId;
            $storyMatchPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING;
            $storyMatchPlayer->m_user_model_prop = json_encode($playerProp, JSON_UNESCAPED_UNICODE);
            $storyMatchPlayer->save();
        }

        if (!empty($userInfo['avatar'])) {
            $userInfo['avatar'] = Attachment::completeUrl($userInfo['avatar']);
        } else {
            $userInfo['avatar'] = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
        }

        return $this->controller->render('knockout_prepare', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'storyMatch'   => $storyMatch,
            'qaId'          => $qaId,
            'matchId'      => $matchId,
            'userInfo'         => $userInfo,
//            'answerType'    => 2,
//            'msg' => $msg,
//            'btnName' => '开始挑战',
        ]);
    }

    public function  generateMathWithCt($ct, $level = 1, $gold = 0) {
        $subjects = [];
        for ($i=0; $i<$ct; $i++) {
            $subjects[] = [
                'level' => $level,
                'topic' => Yii::$app->qas->generateMath($level, $gold),
                'max_time' => 180,
            ];
        }
        return $subjects;
    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
            'answerType' => 1,
        ]);
    }
}