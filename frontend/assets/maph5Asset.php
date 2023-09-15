<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 2:12 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class maph5Asset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    public $css = [
    	'css/bootstrap/bootstrap.min.css',
    ];
    public $js = [
    	'js/jquery/jQuery-2.1.3.min.js',
        'js/jquery/jquery.json.min.js',
        'js/alert.js',
        'html/h5/map.js',
        ];


}