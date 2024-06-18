<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\shoph5;


use common\definitions\Common;
use common\models\ShopWares;
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

class Shop extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $storyModelClass = !empty($_GET['story_model_class']) ? $_GET['story_model_class'] : '';

        $model = ShopWares::find()
//            ->joinWith('model', 'storyModel', 'sessionModel')
//            ->joinWith('storyModel')
            ->where([
                'user_id'       => $userId,
                'session_id'    => $sessionId,
                'is_delete'     => Common::STATUS_NORMAL,
            ]);
        if (!empty($storyModelClass)) {
//            $model = $model->join()
            $model = $model->andFilterWhere(['o_story_model.story_model_class' => $storyModelClass]);
        }
        $model = $model->orderBy(['id' => SORT_DESC])
            ->toArray()
            ->all();

        $userScore = UserScore::find()
            ->where([
                'user_id'       => $userId,
                'session_id'    => $sessionId,
                'story_id'      => $storyId
            ])
            ->one();


        if (!empty($model)) {
            foreach ($model as &$data) {
                switch ($data['link_type']) {
                    case ShopWares::LINK_TYPE_STORY_MODEL:
                    default:
                        $data['item'] = StoryModels::findOne($data['link_id']);
                        break;
                }
            }
        }

        $allParams = $_GET;
        unset($allParams['story_model_class']);
        $params = http_build_query($allParams);

        $template = 'shop_wares';

        return $this->controller->render($template, [
            'model'         => $model,
            'params'        => $_GET,
            'userId'        => $userId,
            'userScore'     => $userScore,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
        ]);
    }
}