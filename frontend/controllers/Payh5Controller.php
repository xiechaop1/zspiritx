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

class Payh5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_phone.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'pay' => [
                'class' => 'frontend\actions\payh5\Pay',
            ],

        ];
    }
}