<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 2:12 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class Secreth5Asset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    CONST HOST = 'https://file.zspiritx.com.cn/static/';

    public $css = [
    	'https://fonts.googleapis.com/css?family=Orbitron:400,500,700,900',
        'https://fonts.googleapis.com/css?family=Share+Tech+Mono',
        'html/html5_sliding_unlock/css/reset.min.css',
        'html/html5_sliding_unlock/css/flickity.css',
        'html/html5_sliding_unlock/css/style.css',
//        'html/h5/diy.css',

    ];
    public $js = [
        self::HOST . 'js/jquery/jQuery-2.1.3.min.js',
        self::HOST . 'js/jquery/jquery.json.min.js',
        'html/html5_sliding_unlock/js/flickity.pkgd.js',
        'html/html5_sliding_unlock/js/howler.js',
        'html/html5_sliding_unlock/js/index.js',
        ];


}