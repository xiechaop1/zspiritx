<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\matchh5;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\Actions;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\StoryMatch;
use common\models\User;
use common\models\UserLottery;
use common\models\UserPrize;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ShowMatch extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;

        try {
            if (!empty($matchId)) {
                $storyMatch = StoryMatch::find()
                    ->where([
                        'id' => $matchId
                    ])
                    ->one();
            } else {
                $storyMatch = StoryMatch::find()
                    ->where([
                        'story_id' => $storyId,
                        'match_type' => StoryMatch::MATCH_TYPE_BATTLE,
                        'user_id' => $userId,
                        'story_match_status' => StoryMatch::STORY_MATCH_STATUS_END
                    ])
                    ->orderBy('id desc')
                    ->one();
            }

            $storyMatchRet = !empty($storyMatch['ret']) ? json_decode($storyMatch['ret'], true) : [];

            // 清空战斗场景
            $clearScenario[] = [
//                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                'performerId' => 'WorldRoot',
                'animationName' => 'Clear',
            ];

            $scenario[] = [
                'timeSinceLast' => 0,
                'lstPerforms' => $clearScenario,
            ];

            $expirationInterval = 600;
            Yii::$app->act->addWithTag($sessionId, 0, $storyId, $userId, $scenario, Actions::ACTION_TYPE_MODEL_DISPLAY, $expirationInterval, 0, 'performList');


            // 切换回大地图场景
            $expirationInterval = 60;        // 消息超时时间
            $stageUId = 'LJ-WORLD';
            Yii::$app->act->add((int)$sessionId, 0, (int)$storyId, (int)$userId, $stageUId, Actions::ACTION_TYPE_CHANGE_STAGE, $expirationInterval);

        } catch (\Exception $e) {
            throw $e;
        }

        return $this->controller->render('show_match', [
            'model'            => $storyMatch,
            'ret'              => $storyMatchRet,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
        ]);

    }
}