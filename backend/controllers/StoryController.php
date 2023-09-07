<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/8
 * Time: 10:49 AM
 */

namespace backend\controllers;


use common\models\Music;
use liyifei\base\controllers\ViewController;
use liyifei\base\helpers\Net;
use yii;

class StoryController extends ViewController
{
    public function behaviors()
    {
        return yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['story', 'edit', 'session_edit', 'user_story_edit', 'session', 'user_story',],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return yii\helpers\ArrayHelper::merge(parent::actions(), [
            'story' => [
                'class' => 'backend\actions\story\Story',
            ],
            'session' => [
                'class' => 'backend\actions\story\Session',
            ],
            'user_story_edit' => [
                'class' => 'backend\actions\story\UserStoryEdit',
            ],
            'edit' => [
                'class' => 'backend\actions\story\Edit',
            ],
            'user_story' => [
                'class' => 'backend\actions\story\UserStory',
            ],
        ]);
    }
}