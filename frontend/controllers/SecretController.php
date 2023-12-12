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

class Qah5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_secret.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'qa_one' => [
                'class' => 'frontend\actions\qah5\QaOne',
            ],

        ];
    }
}