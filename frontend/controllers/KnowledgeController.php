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

class KnowledgeController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'complete_knowledge' => [
                'class' => 'frontend\actions\knowledge\KnowledgeApi',
                'action' => 'complete_knowledge',
            ],
            'get_knowledge_by_user' => [
                'class' => 'frontend\actions\knowledge\KnowledgeApi',
                'action' => 'get_knowledge_by_user',
            ],

        ];
    }
}