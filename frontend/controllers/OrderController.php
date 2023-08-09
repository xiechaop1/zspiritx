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

class OrderController extends Controller
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
                'class'     => 'frontend\actions\order\OrderApi',
                'action'    => 'order',
            ],
            'create' => [
                'class'     => 'frontend\actions\order\OrderApi',
                'action'    => 'create',
            ],
            'lock' => [
                'class'     => 'frontend\actions\order\OrderApi',
                'action'    => 'lock',
            ],
            'unlock' => [
                'class'     => 'frontend\actions\order\OrderApi',
                'action'    => 'unlock',
            ],
            'pay' => [
                'class' => 'frontend\actions\order\OrderApi',
                'action'    => 'pay',
//                'sn' => $request->get('sn')
            ],
            'success' => [
                'class' => 'frontend\actions\order\OrderApi',
                'action'    => 'success',
            ],
            'query' => [
                'class' => 'frontend\actions\order\Query',
                'sn' => $request->get('sn')
            ],
            'cancel' => [
                'class' => 'frontend\actions\order\OrderApi',
                'action'    => 'cancel',
            ],
        ]);
    }
}