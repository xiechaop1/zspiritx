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

class Myh5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_h5.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'my' => [
                'class' => 'frontend\actions\myh5\My',
            ],
            'settings' => [
                'class' => 'frontend\actions\myh5\Settings',
            ],
            'wrong' => [
                'class' => 'frontend\actions\myh5\Wrong',
            ],
            'orders' => [
                'class' => 'frontend\actions\myh5\Orders',
            ],
        ];
    }
}