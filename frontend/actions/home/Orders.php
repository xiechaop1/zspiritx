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

class Orders extends Action
{

    
    public function run()
    {

        $userId = Cookie::getCookie('user_id');
        if (empty($userId)) {
            header('Location: /passport/web_login');
        }

        $orders = [];
        try {

            $orders = Order::find()
                ->where(['user_id' => $userId])
                ->orderBy('id desc')
                ->all();

            foreach ($orders as &$order) {
                if (!empty($order->story)) {
                    $order->story->cover_image = Attachment::completeUrl($order->story->cover_image, true);
                }
            }

        } catch (\Exception $e) {
            //Yii::error($e->getMessage());
        }

        return $this->controller->render('orders', [
            'userId'    => $userId,
            'orderList'  => $orders,
        ]);
    }
}