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

class QaController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'get_qa_list' => [
                'class' => 'frontend\actions\qa\QaApi',
                'action' => 'get_qa_list',
            ],
            'get_session_qa_list' => [
                'class' => 'frontend\actions\qa\QaApi',
                'action' => 'get_session_qa_list',
            ],
            'get_user_qa_list' => [
                'class' => 'frontend\actions\qa\QaApi',
                'action' => 'get_user_qa_list',
            ],
            'get_qa' => [
                'class' => 'frontend\actions\qa\QaApi',
                'action' => 'get_qa',
            ],
            'add_user_answer' => [
                'class' => 'frontend\actions\qa\QaApi',
                'action' => 'add_user_answer',
            ],

        ];
    }
}