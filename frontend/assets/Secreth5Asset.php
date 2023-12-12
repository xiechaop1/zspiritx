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

    public $css = [
    	'https://fonts.googleapis.com/css?family=Orbitron:400,500,700,900',
        'https://fonts.googleapis.com/css?family=Share+Tech+Mono',
        'html/html5_sliding_unlock/css/reset.min.css',
        'html/html5_sliding_unlock/css/flickity.css',
        'html/html5_sliding_unlock/css/style.css',

    ];
    public $js = [
    	'js/jquery/jQuery-2.1.3.min.js',
        'js/jquery/jquery.json.min.js',
        'html/html5_sliding_unlock/js/flickity.pkgd.js',
        'html/html5_sliding_unlock/js/howler.js',
        'html/html5_sliding_unlock/js/index.js',
        ];


}