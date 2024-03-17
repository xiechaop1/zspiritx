<?php

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    const HOST_URL = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/backend_resource/';

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        self::HOST_URL . 'css/site.css',
        self::HOST_URL . 'css/theme.css',
        self::HOST_URL . 'css/bootstrap-datepicker.css',
        self::HOST_URL . 'css/AdminLTE.min1.0.css'
    ];
    public $js = [
        self::HOST_URL . 'script/common.js',
        self::HOST_URL . 'script/form-check.js',
        self::HOST_URL . 'script/bootstrap-datepicker.js',
        self::HOST_URL . 'script/app.js'
    ];
    
    public $jsOptions = [
        'position' => View::POS_HEAD
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
