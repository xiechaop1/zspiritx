<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/19
 * Time: 2:27 PM
 */

namespace frontend\controllers;


use yii\helpers\ArrayHelper;
use yii\web\Controller;

class OrderdataController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';

    public $enableCsrfValidation = false;

//    public function behaviors()
//    {
//        return ArrayHelper::merge(parent::behaviors(), [
//            'access' => [
//                'class' => 'yii\filters\AccessControl',
//                'only' => ['create', 'lock', 'unlock', 'pay', 'query',],
//                'rules' => [
//                    [
//                        'actions' => ['create', 'lock', 'unlock', 'pay', 'query', ],
//                        'allow' => true,
//                        'roles' => ['@']
//                    ]
//                ],
//            ],
//            'verbs' => [
//                'class' => 'yii\filters\VerbFilter',
//                'actions' => [
//                    'create' => ['POST'],
//                    'cancel' => ['POST'],
//                    'pay' => ['GET']
//                ],
//            ]
//        ]);
//    }

    public function actions()
    {
        $request = \Yii::$app->request;

        return ArrayHelper::merge(parent::actions(), [
            'order' => [
                'class'     => 'frontend\actions\order\GetOrderApi',
                'action'    => 'get_order_list',
            ],
        ]);
    }
}