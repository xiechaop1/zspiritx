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
            'join' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'join',
            ],
            'get_session_models' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_session_models',
            ],
            'pickup' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'pickup',
            ],
            'get_baggage_models' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_baggage_models',
            ],
            'finish' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'finish',
            ],
        ];
    }
}