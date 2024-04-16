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

class Prepare extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $matchName = !empty($_GET['match_name']) ? $_GET['match_name'] : '中国勒芒';

        $storyModelId = !empty($_GET['story_model_id']) ? $_GET['story_model_id'] : 0;
        $storyModelDetailId = !empty($_GET['story_model_detail_id']) ? $_GET['story_model_detail_id'] : 0;
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

        if (!empty($storyMatch)) {
            $msg = '您已经有待比赛的车辆，您需要取消或者结束比赛！';
//            return $this->renderErr('您已经有待比赛的车辆，您需要取消或者结束比赛！');
//            throw new Exception('您已经有待比赛的车辆，您需要取消或者结束比赛！', ErrorCode::STORY_MATCH_ALREADY_EXIST_READY);
        } else {

            $userModel = UserModels::find()
                ->where([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'story_id' => $storyId,
//                'story_model_id' => $storyModelId,
                ]);
            if (!empty($storyModelDetailId)) {
                $userModel->andWhere(['story_model_detail_id' => $storyModelDetailId]);
            } else {
                $userModel->andWhere(['story_model_id' => $storyModelId]);
            }

            $userModel = $userModel->one();

//        if (!empty($userModel)) {
//            $storyModelId = $userModel->story_model_id;
//            $storyModelDetailId = $userModel->story_model_detail_id;
//        } else {
//            $storyModelId = 0;
//            $storyModelDetailId = 0;
//        }

            $storyMatch = new StoryMatch();
            $storyMatch->story_id = $storyId;
            $storyMatch->user_id = $userId;
            $storyMatch->session_id = $sessionId;
            $storyMatch->match_name = $matchName;
            $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_PREPARE;
            $storyMatch->user_model_id = $userModel->id;
            $storyMatch->m_story_model_id = $storyModelId;
            $storyMatch->m_story_model_detail_id = $storyModelDetailId;
            $storyMatch->save();

            $msg = '您的车辆已经准备好，准备开始比赛吧！';
        }

        return $this->controller->render('msg', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'story_match'   => $storyMatch,
            'msg' => $msg,
            'btnName' => '开始比赛',
        ]);
    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}