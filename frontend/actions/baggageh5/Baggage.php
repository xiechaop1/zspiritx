<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\baggageh5;


use common\definitions\Common;
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

class Baggage extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $targetStoryModelId = !empty($_GET['target_story_model_id']) ? $_GET['target_story_model_id'] : 0;
        $targetStoryModelDetailId = !empty($_GET['target_story_model_detail_id']) ? $_GET['target_story_model_detail_id'] : 0;
        $targetModelId = !empty($_GET['target_model_id']) ? $_GET['target_model_id'] : 0;

        $targetUserModelLocId = !empty($_GET['target_user_model_loc_id']) ? $_GET['target_user_model_loc_id'] : 0;

        $storyModelClass = !empty($_GET['story_model_class']) ? $_GET['story_model_class'] : '';

        $action = !empty($_GET['action']) ? $_GET['action'] : '';

        if ($storyId != 5) {
            $model = UserModels::find()
//            ->joinWith('model', 'storyModel', 'sessionModel')
                ->joinWith('storyModel')
                ->where([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'is_delete' => Common::STATUS_NORMAL,
                ]);
        } else {
            $model = UserModels::find()
                ->joinWith('storyModel')
                ->where([
                    'user_id' => $userId,
                    'o_user_model.story_id' => $storyId,
                    'is_delete' => Common::STATUS_NORMAL,
                ]);
        }
        if (!empty($storyModelClass)) {
//            $model = $model->join()
            $model = $model->andFilterWhere(['o_story_model.story_model_class' => $storyModelClass]);
        }
        $model = $model->orderBy(['id' => SORT_DESC])
            ->all();

//        if ($storyId != 5) {
//            $userScore = UserScore::find()
//                ->where([
//                    'user_id' => $userId,
//                    'session_id' => $sessionId,
//                    'story_id' => $storyId
//                ])
//                ->one();
//        } else {
//            $userScore = UserScore::find()
//                ->where([
//                    'user_id' => $userId,
//                    'story_id' => $storyId,
//                ])
//                ->one();
//        }
        $scoreSessionId = $sessionId;
        if ($storyId == 5) {
            $scoreSessionId = 0;
        }

        $userScore = Yii::$app->score->get($userId, $storyId, $scoreSessionId);

//        $template = 'baggage';

        $allParams = $_GET;
        unset($allParams['story_model_class']);
        $params = http_build_query($allParams);

        $title = '背包';
        $title2 = '<a href="/baggageh5/all?' . $params . '&story_model_class=3">对战</a>';

        if ( !empty($storyModelClass) ) {
            if ($storyModelClass == StoryModels::STORY_MODEL_CLASS_PET) {
                $title = '对战';
                $title2 = '<a href="/baggageh5/all?' . $params . '">背包</a>';
            } else {
                $title = StoryModels::$storyModelClass2Name[$storyModelClass];
                $title2 = '<a href="/baggageh5/all?' . $params . '">背包</a>';
            }
        }

        $template = 'baggage_v2';
        $bagVersion = !empty($_GET['bag_version']) ? $_GET['bag_version'] : 0;
        if (!empty($bagVersion)
            || in_array($storyId,[5,7,10,11,12,15])
        ) {
            $template = 'baggage_v2';
        }

        $setActiveType = !empty($_GET['set_active_type']) ? $_GET['set_active_type'] : 0;

        return $this->controller->render($template, [
            'title'         => $title,
            'title2'        => $title2,
            'model'         => $model,
            'params'        => $_GET,
            'userId'        => $userId,
            'userScore'     => $userScore,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'action'        => $action,
            'targetStoryModelId' => $targetStoryModelId,
            'targetStoryModelDetailId' => $targetStoryModelDetailId,
            'targetModelId' => $targetModelId,
            'targetUserModelLocId' => $targetUserModelLocId,
            'setActiveType' => $setActiveType,
        ]);
    }
}