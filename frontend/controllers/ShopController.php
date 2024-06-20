<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午11:30
 */

namespace frontend\controllers;


use yii\web\Controller;

class ShopController extends Controller
{
    public $layout = '@frontend/views/layouts/main_n.php';

    public function actions()
    {
        return [
            'buy' => [
                'class'     => 'frontend\actions\shop\ShopApi',
                'action'    => 'buy',
            ],
        ];
    }
}