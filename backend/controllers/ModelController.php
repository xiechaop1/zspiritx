<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/8
 * Time: 10:49 AM
 */

namespace backend\controllers;


use liyifei\base\controllers\ViewController;
use liyifei\base\helpers\Net;
use yii;

class ModelController extends ViewController
{
    public function behaviors()
    {
        return yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['story_model', 'story_model_edit', 'models_edit', 'session_model', 'models', 'story_stage', 'story_stage_edit'],
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
            'story_model' => [
                'class' => 'backend\actions\model\StoryModel',
            ],
            'session_model' => [
                'class' => 'backend\actions\model\SessionModel',
            ],
            'models' => [
                'class' => 'backend\actions\model\Models',
            ],
            'models_edit' => [
                'class' => 'backend\actions\model\ModelsEdit',
            ],
            'story_model_edit' => [
                'class' => 'backend\actions\model\StoryModelEdit',
            ],
            'story_stage' => [
                'class' => 'backend\actions\model\StoryStage',
            ],
            'story_stage_edit' => [
                'class' => 'backend\actions\model\StoryStageEdit',
            ],
        ]);
    }
}