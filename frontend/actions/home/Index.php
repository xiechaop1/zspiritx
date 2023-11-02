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

class Index extends Action
{

    
    public function run()
    {
//var_dump($_SESSION);

        $image = 'img/home/index_image.jpg';
        $image = Attachment::completeUrl($image, true);

//        $banner = [
//            'zhuluoji' => Attachment::completeUrl('img/home/konglong2.jpg', true),
//            'taoranting' => Attachment::completeUrl('img/home/taoranting1.jpg', true),
//            'senlin' => Attachment::completeUrl('img/home/index_image.jpg', true),
//        ];

//        $userId = !empty($_SESSION['user_info']['id']) ? $_SESSION['user_info']['id'] : 0;

        $userId = Cookie::getCookie('user_id');
        if (empty($userId)) {
            header('Location: /passport/web_login');
        }

        $stories = Story::find()
            ->where(['story_status' => Story::STORY_STATUS_ONLINE])
            ->orderBy(['sort_by' => SORT_ASC])
            ->all();

        $orders = Order::find()
            ->where([
                'user_id'   => $userId,
            ])
            ->all();

        try {
            $user = User::find()
                ->where(['id' => $userId])
                ->one();

            $user->last_login_time = time();
            $user->last_login_device = Client::getAgent();
            $user->save();
        } catch (\Exception $e) {
            //Yii::error($e->getMessage());
        }

        $ordersMap = [];
        foreach ($orders as $order) {
            $ordersMap[$order->story_id] = $order->order_status;
        }

        $bgm = '';

        return $this->controller->render('index', [
            'userId'    => $userId,
            'stories'   => $stories,
            'orders'    => $orders,
            'ordersMap' => $ordersMap,
            'voice' => '',
            'image' => $image,
            'bgSound' => $bgm,
//            'banner' => $banner,
        ]);
    }
}