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
use common\models\Poem;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryRank;
use common\models\User;
use common\models\UserLottery;
use common\models\UserModelLoc;
use common\models\UserModels;
use common\models\UserPrize;
use common\models\UserScore;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Knockout extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
//        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

//        $userModelId = !empty($_GET['user_model_id']) ? $_GET['user_model_id'] : 0;

        $qa = Qa::find()
            ->where([
                'id'    => $qaId,
            ])
            ->one();

        if (!empty($qa)) {
            $qa = $qa->toArray();
            $qa['selected_json'] = \common\helpers\Common::isJson($qa['selected']) ? json_decode($qa['selected'], true) : $qa['selected'];
        }

        $storyMatch = StoryMatch::find()
            ->where([
                'id'    => $matchId,
//                'match_type' => StoryMatch::MATCH_TYPE_CHALLENGE,
                'story_match_status' => [
                    StoryMatch::STORY_MATCH_STATUS_MATCHING,
                    StoryMatch::STORY_MATCH_STATUS_PLAYING
                ],
//                'user_model_id' => $userModelId,
            ])
            ->one();

        $user = User::find()
            ->where([
                'id'    => $userId,
            ])
            ->one();

        if (!empty($user['avatar'])) {
            $user['avatar'] = Attachment::completeUrl($userInfo['avatar']);
        } else {
            $user['avatar'] = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
        }

        if (empty($user)) {
            return $this->renderErr('用户不存在！');
        }

        if (empty($storyMatch)) {
            return $this->renderErr('挑战还没有准备好！');
//            throw new Exception('您没有准备参赛的赛车，请您用钥匙启动准备好以后，联系小精灵！', ErrorCode::STORY_MATCH_NOT_EXIST_READY);
        }

        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_PLAYING;
        $storyMatch->save();

        $playersData = $storyMatch->players;

        $players = [];
        if (!empty($playersData)) {
            foreach ($playersData as $player) {
                if ($player->user_id == $userId
                    && !in_array($player->match_player_status
                ,[StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING
                        ,StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING
                        ,StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE])) {
                    return $this->renderErr('您已经结束比赛！');
                }
                $tmp = $player->toArray();
                if (!empty($player->user)) {
                    $tmp['user'] = $player->user->toArray();
                } else {
                    $tmp['user']['user_name'] = 'AI-' . rand(1000,9999);
                }
                if (!empty($tmp['user']['avatar'])) {
                    $tmp['user']['avatar'] = Attachment::completeUrl($tmp['user']['avatar'], 60);
                } else {
                    $tmp['user']['avatar'] = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
                }

                $players[] = $tmp;
            }
        }
        $storyMatchPlayers = $players;

        $subjectsJson = $storyMatch->story_match_prop;
        $subjects = json_decode($subjectsJson, true);

//        var_dump($subjects);exit;
//        // 保存比赛状态
//        $storyMatch->match_detail = json_encode($matchDetail, true);
//        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
////        $storyMatch->save();
//
////        $matchFlow['flow'] = $matchDetail;

        return $this->controller->render('knockout', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'matchId'       => $matchId,
            'subjects'      => $subjects,
            'matchPlayers'  => $storyMatchPlayers,
            'qa'            => $qa,
            'rtnAnswerType' => 2,
            'subjectsJson' => json_encode($subjects, JSON_UNESCAPED_UNICODE),
            'ct'            => !empty($subjects['subjects']) ? sizeof($subjects['subjects']) : 0,
            'storyMatch'   => $storyMatch,
            'initTimer' => 180,
            'user'          => $user,
        ]);
    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}