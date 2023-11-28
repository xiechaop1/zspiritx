<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 2:12 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class Compassh5Asset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    public $css = [
        'css/theme.css',
        'html/h5/app.css',
        'html/h5/qa.css'
    ];
    public $js = [
        'js/jquery/jQuery-2.1.3.min.js',
        'js/jquery/jquery.json.min.js',
        'js/bootstrap/bootstrap.min.js',
        'html/h5/rap.js',
        'html/h5/hammer.js',
        'html/h5/compass.js',
        'html/h5/app.js',
        ];


}