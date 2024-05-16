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

class Matchh5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_h5.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'prepare' => [
                'class' => 'frontend\actions\matchh5\Prepare',
            ],
            'play' => [
                'class' => 'frontend\actions\matchh5\Play',
            ],
            'rank_of_match' => [
                'class' => 'frontend\actions\matchh5\Rankofmatch',
            ],
            'battle' => [
                'class' => 'frontend\actions\matchh5\Battle',
            ],
            'battle_prepare' => [
                'class' => 'frontend\actions\matchh5\BattlePrepare',
            ],
        ];
    }
}