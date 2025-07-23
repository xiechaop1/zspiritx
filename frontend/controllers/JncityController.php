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
    public $layout = '@frontend/views/layouts/main_w.php';

    public function actions()
    {
        return [
            'poi_list' => [
                'class' => 'frontend\actions\jncity\JncityApi',
                'action' => 'poi_list',
            ],
            'upload' => [
                'class'     => 'frontend\actions\jncity\Upload',
            ],
            'get_story' => [
                'class' => 'frontend\actions\jncity\JncityApi',
                'action' => 'get_story',
            ],
            'get_user_ebook' => [
                'class' => 'frontend\actions\jncity\JncityApi',
                'action' => 'get_user_ebook',
            ],
            'get_user_last_ebook' => [
                'class' => 'frontend\actions\jncity\JncityApi',
                'action' => 'get_user_last_ebook',
            ],
            'menu' => [
                'class'     => 'frontend\actions\jncity\Menu',
            ],
            'generate_video' => [
                'class' => 'frontend\actions\jncity\JncityApi',
                'action' => 'generate_video',
            ],
        ];
    }
}