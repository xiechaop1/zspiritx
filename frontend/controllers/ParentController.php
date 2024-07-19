<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午11:30
 */

namespace frontend\controllers;


use liyifei\base\controllers\ViewController;
use yii\web\Controller;

class ParentController extends Controller
{
    public $layout = '@frontend/views/layouts/main_n.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'data' => [
                'class' => 'frontend\actions\parent\ParentApi',
                'action' => 'get_data',
            ],
            'get_shop_wares' => [
                'class' => 'frontend\actions\parent\ParentApi',
                'action' => 'get_shop_wares',
            ],
            'get_one_shop_ware' => [
                'class' => 'frontend\actions\parent\ParentApi',
                'action' => 'get_one_shop_ware',
            ],
            'get_orders' => [
                'class' => 'frontend\actions\parent\ParentApi',
                'action' => 'get_orders',
            ],
            'get_subjects_his_by_user' => [
                'class' => 'frontend\actions\parent\ParentApi',
                'action' => 'get_subjects_his_by_user',
            ],


        ];
    }
}