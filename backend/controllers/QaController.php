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
                        'actions' => ['qa', 'edit', 'user_qa', 'poem', 'poem_edit', 'qa_package', 'package_edit'],
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
            'qa_package' => [
                'class' => 'backend\actions\qa\QaPackage',
            ],
            'package_edit' => [
                'class' => 'backend\actions\qa\PackageEdit',
            ],
            'user_qa' => [
                'class' => 'backend\actions\qa\Userqa',
            ],
            'poem' => [
                'class' => 'backend\actions\qa\Poem',
            ],
            'poem_edit' => [
                'class' => 'backend\actions\qa\PoemEdit',
            ],
        ]);
    }
}