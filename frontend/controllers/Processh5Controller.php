<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午11:30
 */

namespace frontend\controllers;


use yii\web\Controller;

class Processh5Controller extends Controller
{
    public $layout = '@frontend/views/layouts/main_h5.php';

    public function actions()
    {
        return [
            'pickup' => [
                'class'     => 'frontend\actions\processh5\Pickup',
            ],
            'finish' => [
                'class'     => 'frontend\actions\processh5\Finish',
            ],
            'guide' => [
                'class'     => 'frontend\actions\processh5\Guide',
            ],
            'actions' => [
                'class'     => 'frontend\actions\processh5\Actions',
            ],
            'set_use_models' => [
                'class'     => 'frontend\actions\processh5\SetUseModels',
            ],
            'set_session_model' => [
                'class'     => 'frontend\actions\processh5\SetSessionModel',
            ],
            'set_home' => [
                'class'     => 'frontend\actions\processh5\SetHome',
            ],
            'menu' => [
                'class'     => 'frontend\actions\processh5\Menu',
            ],
        ];
    }
}