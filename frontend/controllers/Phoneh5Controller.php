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

class Phoneh5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_phone.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'phone' => [
                'class' => 'frontend\actions\phoneh5\Phone',
            ],
            'mail' => [
                'class' => 'frontend\actions\phoneh5\Mail',
            ],
            'sms' => [
                'class' => 'frontend\actions\phoneh5\Sms',
            ],

        ];
    }
}