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

class VoiceController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'input' => [
                'class' => 'frontend\actions\voice\VoiceApi',
                'action' => 'input',
            ],
        ];
    }
}