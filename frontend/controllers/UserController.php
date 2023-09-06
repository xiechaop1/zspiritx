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

class UserController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'get_user' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_user',
            ],
            'get_token' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_token',
            ],
            'get_session' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_session',
            ],
            'get_mobile' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_mobile',
            ],
            'update_user' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'update_user',
            ],
            'login' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'login',
            ],
            'new_user' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'new_user',
            ],

            'update_user_loc' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'update_user_loc',
            ],
            'get_user_loc_by_team' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_user_loc_by_team',
            ],
            'get_user_loc' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_user_loc',
            ],
            'get_user_list_by_story' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_user_list_by_story',
            ],
            'get_user_list_by_session' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_user_list_by_session',
            ],
            'get_user_list_by_team' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_user_list_by_team',
            ],
            'get_user_score_rank' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_user_score_rank',
            ],
            'add_user_score' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'add_user_score',
            ],
            'get_user_score' => [
                'class' => 'frontend\actions\user\UserApi',
                'action' => 'get_user_score',
            ],

        ];
    }
}