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
use common\models\User;
use common\models\UserModels;
use common\models\UserModelsUsed;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class SetHome extends Action
{

    private $_get;

    private $_params;
    
    public function run()
    {
        $this->_get = Yii::$app->request->get();

        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;
        $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;

        $this->_params = [
            'user_id'    => $userId,
            'session_id'    => $sessionId,
        ];

        try {
            $user = User::find()->where(['id' => $userId])->one();
            if (empty($user->home_lng) || empty($user->home_lat)) {
                $user->home_lng = $userLng;
                $user->home_lat = $userLat;
                $user->save();
            }
            $msg = '迁入了新家！你需要重启一下app，你的新家就完成最后一步啦！';
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