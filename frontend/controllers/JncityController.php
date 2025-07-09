<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午11:30
 */

namespace frontend\controllers;


use yii\web\Controller;

class JncityController extends Controller
{
    public $layout = '@frontend/views/layouts/main_h5.php';

    public function actions()
    {
        return [
            'poi' => [
                'class'     => 'frontend\actions\jncity\Poi',
            ],
            'upload' => [
                'class'     => 'frontend\actions\jncity\Upload',
            ],
            'get_doc' => [
                'class'     => 'frontend\actions\jncity\GetDoc',
            ],
            'menu' => [
                'class'     => 'frontend\actions\jncity\Menu',
            ],
        ];
    }
}