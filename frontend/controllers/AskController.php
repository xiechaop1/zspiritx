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

class AskController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';


    public function actions()
    {
        $this->enableCsrfValidation = false;

        $request = \Yii::$app->request;

        return [
            'say' => [
                'class' => 'frontend\actions\ask\AskApi',
                'action' => 'say',
            ],

        ];
    }
}