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

class OrdersController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';

    public function actions()
    {
        return [
            'create' => [
                'class' => 'frontend\actions\orders\Create'
            ],
            'edit' => [
                'class' => 'frontend\actions\orders\Edit'
            ],
            'change_status' => [
                'class' => 'frontend\actions\orders\ChangeStatus'
            ],
            'upload' => [
                'class' => 'frontend\actions\orders\Upload'
            ],
            'upload_document' => [
                'class' => 'frontend\actions\orders\UploadDocument'
            ],
            'upload_interview' => [
                'class' => 'frontend\actions\orders\UploadInterview'
            ],
            'preview' => [
                'class' => 'frontend\actions\orders\Preview'
            ],
            'contract' => [
                'class' => 'frontend\actions\orders\Contract'
            ],
        ];
    }
}