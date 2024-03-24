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

class LotteryController extends Controller
{
    public $layout = '@frontend/views/layouts/main_w.php';

    public function actions()
    {
        $request = \Yii::$app->request;

        return [
            'award' => [
                'class' => 'frontend\actions\lottery\LotteryApi',
                'action' => 'award',
            ],
            'generate_lottery' => [
                'class' => 'frontend\actions\lottery\LotteryApi',
                'action' => 'generate_lottery',
            ],
            'get_user_lottery' => [
                'class' => 'frontend\actions\lottery\LotteryApi',
                'action' => 'get_user_lottery',
            ],
            'get_user_prize' => [
                'class' => 'frontend\actions\lottery\LotteryApi',
                'action' => 'get_user_prize',
            ],
        ];
    }
}