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

class MatchController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'update_match' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'update_match',
            ],
            'add_knock_player' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'add_knock_player',
            ],
            'update_knock_players' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'update_knock_players',
            ],
            'get_knockout_status' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'get_knockout_status',
            ],
            'get_knockout_players_in_match' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'get_knockout_players_in_match',
            ],
            'get_suggestion_from_subject' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'get_suggestion_from_subject',
            ],
            'get_subject_by_user_ware_id' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'get_subject_by_user_ware_id',
            ],
            'get_subjects' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'get_subjects',
            ],
            'play_voice' => [
                'class' => 'frontend\actions\match\MatchApi',
                'action' => 'play_voice',
            ],

        ];
    }
}