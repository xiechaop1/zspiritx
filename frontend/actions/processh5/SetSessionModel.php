<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\processh5;


use common\definitions\ErrorCode;
use common\models\SessionModels;
use common\models\StoryModels;
use common\models\UserModels;
use common\models\UserModelsUsed;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class SetSessionModel extends Action
{

    private $_get;

    private $_params;
    
    public function run()
    {
        $this->_get = Yii::$app->request->get();

        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $modelId = !empty($this->_get['model_id']) ? $this->_get['model_id'] : 0;
        $storyModelId = !empty($this->_get['story_model_id']) ? $this->_get['story_model_id'] : 0;

        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        try {
            $sessionModel = SessionModels::find()
                ->where([
                    'user_id'    => $userId,
                    'session_id'    => $sessionId,
                    'story_model_id'    => $storyModelId,
//                    'model_id'    => $modelId,
                ]);
            if (!empty($storyId)) {
                $sessionModel->andWhere(['story_id' => $storyId]);
            }
            $sessionModel->one();

            if (!empty($sessionModel)) {
                $sessionModel->set_at = time();
                $sessionModel->save();
                $msg = '使用完成';
            }

        } catch (\Exception $e) {
            return $this->setRender($e->getCode(), $e->getMessage(), $this->_params);
//            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $this->setRender(0, $msg, $this->_params);

    }

    public function setRender($code = 0, $msg = '', $params) {
        return $this->controller->render('msg', [
            'userId'        => $params['user_id'],
            'sessionId'     => $params['session_id'],
            'msg'           => $msg,
        ]);
    }
}