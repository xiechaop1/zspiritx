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

class Knowledgeh5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_h5.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'all' => [
                'class' => 'frontend\actions\knowledgeh5\Knowledge',
            ],
            'set' => [
                'class' => 'frontend\actions\knowledgeh5\Set',
            ],

        ];
    }
}