<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\payh5;


use common\definitions\Common;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Pay extends Action
{

    
    public function run()
    {
        $get = \Yii::$app->request->get();
        $userId = !empty($get['user_id']) ? $get['user_id'] : 0;
        $sessionId = !empty($get['session_id']) ? $get['session_id'] : 0;
        $teamId = !empty($get['team_id']) ? $get['team_id'] : 0;
        $userLng = !empty($get['user_lng']) ? $get['user_lng'] : 0;
        $userLat = !empty($get['user_lat']) ? $get['user_lat'] : 0;
        $storyStageId = !empty($get['story_stage_id']) ? $get['story_stage_id'] : 0;
        $storyId = !empty($get['story_id']) ? $get['story_id'] : 0;
        $disRange = 2000;

        return $this->controller->render('pay', [
            'userId'    => $userId,
            'sessionId' => $sessionId,
            'teamId'    => $teamId,
            'storyId'   => $storyId,
            'userLng'   => $userLng,
            'userLat'   => $userLat,
            'storyStageId' => $storyStageId,
            'disRange'  => $disRange,
        ]);
    }
}