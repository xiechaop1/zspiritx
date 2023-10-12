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

class KnowledgeController extends ViewController
{
    public function behaviors()
    {
        return yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['knowledge', 'edit', 'user_knowledge', 'item_knowledge'],
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
            'knowledge' => [
                'class' => 'backend\actions\knowledge\Knowledge',
            ],
            'edit' => [
                'class' => 'backend\actions\knowledge\Edit',
            ],
            'user_knowledge' => [
                'class' => 'backend\actions\knowledge\UserKnowledge',
            ],
            'item_knowledge' => [
                'class' => 'backend\actions\knowledge\ItemKnowledge',
            ],
        ]);
    }
}