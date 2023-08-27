<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * APP基本静态资源
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    public $css = [
        'css/bootstrap/bootstrap.min.css',
        'css/theme.css',
        'css/iconfont.css',
//        'html/search/search.css'
    ];
    public $js = [
//        'js/jquery/jquery.js',
        'js/Popper/Popper.js',
        'js/bootstrap/bootstrap.min.js',
        'js/jquery.cookie.js',
        'js/cook.js',
        'js/toTop.js',
        'js/alert.js',
        'js/vcode.js',
        'js/header.js',
        'js/CheckForm.js',
        'js/login.js',
        'js/getOptions.js',
        'js/jquery.SuperSlide.2.1.3.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
