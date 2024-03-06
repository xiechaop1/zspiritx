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

class QaController extends ViewController
{
    public function behaviors()
    {
        return yii\helpers\ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['qa', 'edit', 'user_qa'],
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
            'qa' => [
                'class' => 'backend\actions\qa\Qa',
            ],
            'edit' => [
                'class' => 'backend\actions\qa\Edit',
            ],
            'user_qa' => [
                'class' => 'backend\actions\qa\Userqa',
            ],
        ]);
    }
}