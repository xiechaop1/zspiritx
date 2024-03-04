<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\home;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\Order;
use common\models\Story;
use common\models\User;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class My extends Action
{

    
    public function run()
    {

        $userId = Cookie::getCookie('user_id');
        if (empty($userId)) {
            header('Location: /passport/web_login');
        }

        try {
            $user = User::find()
                ->where(['id' => $userId])
                ->one();

            if ($user->user_status == User::USER_STATUS_NORMAL) {

                $user->last_login_time = time();
                $user->last_login_device = Client::getAgent();
                $user->save();
            } else {
                header('Location: /passport/web_login');
            }
        } catch (\Exception $e) {
            //Yii::error($e->getMessage());
        }

        $urls = [
            'privacy'   => '',
            'agreement' => '',
        ];

        $unityVersion = !empty($_GET['unity_version']) ? $_GET['unity_version'] : '';

        return $this->controller->render('my', [
            'userId'    => $userId,
            'userInfo'  => $user,
            'urls'       => $urls,
            'unityVersion' => $unityVersion,
//            'banner' => $banner,
        ]);
    }
}