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

class SetUseModels extends Action
{

    private $_get;

    private $_params;
    
    public function run()
    {
        $this->_get = Yii::$app->request->get();

        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $groupName = !empty($this->_get['group_name']) ? $this->_get['group_name'] : '';
        $modelId = !empty($this->_get['model_id']) ? $this->_get['model_id'] : 0;
        $storyModelId = !empty($this->_get['story_model_id']) ? $this->_get['story_model_id'] : 0;

        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        $this->_params = [
            'user_id'    => $userId,
            'session_id'    => $sessionId,
        ];

        try {
            $ret = Yii::$app->models->addPreUserModelUsedByGroup($groupName, $userId, $storyId, $sessionId);
            $msg = '我已经准备好啦，打开背包，找到物品，点击使用吧！';
        } catch (\Exception $e) {
            return $this->pickupRender($e->getCode(), $e->getMessage(), $this->_params);
//            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $this->pickupRender(0, $msg, $this->_params);

    }

    public function pickupRender($code = 0, $msg = '', $params) {
        return $this->controller->render('msg', [
            'userId'        => $params['user_id'],
            'sessionId'     => $params['session_id'],
            'msg'           => $msg,
        ]);
    }
}