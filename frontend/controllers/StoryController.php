<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午11:30
 */

namespace frontend\controllers;


use yii\web\Controller;

class StoryController extends Controller
{
    public $layout = '@frontend/views/layouts/main_n.php';

    public function actions()
    {
        return [
            'all' => [
                'class'     => 'frontend\actions\story\StoryApi',
                'action'    => 'all',
            ],
            'detail' => [
                'class'     => 'frontend\actions\story\StoryApi',
                'action'    => 'detail',
            ],
            'goal' => [
                'class'     => 'frontend\actions\story\StoryApi',
                'action'    => 'goal',
            ],
        ];
    }
}