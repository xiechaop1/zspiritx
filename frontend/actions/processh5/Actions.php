<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\processh5;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\models\SessionModels;
use common\models\Story;
use common\models\StoryModels;
use common\models\UserModels;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Actions extends Action
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
        $actDetail = !empty($this->_get['act_detail']) ? $this->_get['act_detail'] : 0;
        $actType = !empty($this->_get['act_type']) ? $this->_get['act_type'] : \common\models\Actions::ACTION_TYPE_MSG;
        $expirationInterval = !empty($this->_get['expiration_interval']) ? $this->_get['expiration_interval'] : 0;

        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        try {
            $ret = Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, $actDetail, $actType, $expirationInterval);
            $msg = '消息成功发送';
        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }

        return $this->controller->render('actions', [
            'model'            => $ret,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'msg'           => $msg,
        ]);

    }

}