<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\cardh5;


use common\definitions\Common;
use common\helpers\Attachment;
use common\models\Story;
use common\models\StoryModels;
use common\models\UserModels;
use common\models\UserScore;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Card extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $storyModelId = !empty($_GET['story_model_id']) ? $_GET['story_model_id'] : 0;

        $storyModel = StoryModels::find()
            ->where([
                'id'    => $storyModelId,
            ])
            ->one();

        $storyModel = $storyModel->toArray();

        if (!empty($storyModel['story_model_image'])) {
            $storyModel['story_model_image'] = Attachment::completeUrl($storyModel['story_model_image'], true);
        }


        return $this->controller->render('card', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'storyModel'    => $storyModel,
            'storyModelId'  => $storyModelId,
        ]);
    }
}