<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午11:30
 */

namespace frontend\controllers;


use yii\web\Controller;

class ProcessController extends Controller
{
    public $layout = '@frontend/views/layouts/main_n.php';

    public function actions()
    {
        return [
            'init' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'init',
            ],
            'get_models' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_models',
            ],
        ];
    }
}