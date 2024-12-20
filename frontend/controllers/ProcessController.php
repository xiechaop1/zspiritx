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
            'get_story' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_story',
            ],
            'get_session_models' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_session_models',
            ],
            'get_session_models_by_stage' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_session_models_by_stage',
            ],
            'get_session_models_init' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_session_models_init',
            ],
            'get_session_stages' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_session_stages',
            ],
            'get_user_model_loc' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_user_model_loc',
            ],
            'use_model' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'use_model',
            ],
            'phone_call' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'phone_call',
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
            'get_action_by_user' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'get_action_by_user',
            ],
            'update_story_model' => [
                'class'     => 'frontend\actions\process\DoApi',
                'action'    => 'update_story_model',
            ],
        ];
    }
}