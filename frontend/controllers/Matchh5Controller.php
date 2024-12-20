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
            'race_prepare' => [
                'class' => 'frontend\actions\matchh5\RacePrepare',
            ],
            'race' => [
                'class' => 'frontend\actions\matchh5\Race',
            ],
            'battle' => [
                'class' => 'frontend\actions\matchh5\Battle',
            ],
            'battle_prepare' => [
                'class' => 'frontend\actions\matchh5\BattlePrepare',
            ],
            'challenge' => [
                'class' => 'frontend\actions\matchh5\Challenge',
            ],
            'challenge_prepare' => [
                'class' => 'frontend\actions\matchh5\ChallengePrepare',
            ],
            'knockout_prepare' => [
                'class' => 'frontend\actions\matchh5\KnockoutPrepare',
            ],
            'knockout' => [
                'class' => 'frontend\actions\matchh5\Knockout',
            ],
            'practice' => [
                'class' => 'frontend\actions\matchh5\Practice',
            ],
            'show_match' => [
                'class' => 'frontend\actions\matchh5\ShowMatch',
            ],
            'stories' => [
                'class' => 'frontend\actions\matchh5\Stories',
            ],
            'docs' => [
                'class' => 'frontend\actions\matchh5\Docs',
            ],
            'puzzle' => [
                'class' => 'frontend\actions\matchh5\Puzzle',
            ],

        ];
    }
}