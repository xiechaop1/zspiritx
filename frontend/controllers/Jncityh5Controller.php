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

class Jncityh5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_h5.php';
    public $enableCsrfValidation = false;
    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'uploadimage' => [
                'class' => 'frontend\actions\jncityh5\UploadImage',
            ],
        ];
    }
}