<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\compassh5;


use common\definitions\Common;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Compass extends Action
{

    public $tpl = 'compass';
    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyStageId = !empty($_GET['story_stage_id']) ? $_GET['story_stage_id'] : 0;
        $teamId = !empty($_GET['team_id']) ? $_GET['team_id'] : 0;
        $userLng = !empty($_GET['user_lng']) ? $_GET['user_lng'] : 0;
        $userLat = !empty($_GET['user_lat']) ? $_GET['user_lat'] : 0;
        $targetLng = !empty($_GET['target_lng']) ? $_GET['target_lng'] : 0;
        $targetLat = !empty($_GET['target_lat']) ? $_GET['target_lat'] : 0;
        $disRange = !empty($_GET['dis_range']) ? $_GET['dis_range'] : 0;


        return $this->controller->render($this->tpl, [
            'userId'        => $userId,
            'storyId'       => $storyId,
            'sessionId'     => $sessionId,
            'storyStageId'  => $storyStageId,
            'teamId'        => $teamId,
            'userLng'       => $userLng,
            'userLat'       => $userLat,
            'targetLng'     => $targetLng,
            'targetLat'     => $targetLat,
            'disRange'      => $disRange,
        ]);
    }
}