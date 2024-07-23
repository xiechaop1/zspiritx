<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\myh5;


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

        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : '';

        $itemType = !empty(Yii::$app->request->get('item_type')) ? Yii::$app->request->get('item_type') : Order::ITEM_TYPE_STORY;

        $page = !empty(Yii::$app->request->get('page')) ? Yii::$app->request->get('page') : 1;
        $limit = 10;

        $orders = [];
        try {

            $orders = Order::find()
                ->where([
                    'user_id' => $userId,
                    'item_type' => $itemType,
                ])
                ->orderBy('id desc')
                ->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->all();

            if (count($orders) > $limit) {
                $next = true;
            } else {
                $next = false;
            }

            array_pop($orders);


//            foreach ($orders as &$order) {
//                switch ($order->item_type) {
//                    case Order::ITEM_TYPE_STORY:
//                    default:
//                        if (!empty($order->story)) {
//                            $order->story->cover_image = Attachment::completeUrl($order->story->cover_image, true);
//                        }
//                        break;
//                }
//
//            }

        } catch (\Exception $e) {
            //Yii::error($e->getMessage());
        }

        return $this->controller->render('orders', [
            'userId'    => $userId,
            'orderList'  => $orders,
            'itemType'  => $itemType,
            'page'      => $page,
            'next'      => $next,
            'params'    => $_GET,
        ]);
    }
}