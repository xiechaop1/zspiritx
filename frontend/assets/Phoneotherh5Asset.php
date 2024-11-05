<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 2:12 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class Phoneotherh5Asset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    CONST HOST = 'https://file.zspiritx.com.cn/static/';
    public $css = [
        self::HOST . 'css/bootstrap/bootstrap.min.css',
        self::HOST . 'js/bootoast/bootoast.css',
        self::HOST . 'css/theme.css',
        self::HOST . 'css/dropdown/dropdowm.css',
        self::HOST . 'css/iconfont.css',
        self::HOST . 'css/datePicker/bootstrap-datepicker.css',
        self::HOST . 'css/bootstrap-clockpicker.css',
        'html/h5_phone/other.css',

    ];
    public $js = [
        self::HOST . 'js/jquery/jQuery-2.1.3.min.js',
        self::HOST . 'js/jquery/jquery.json.min.js',
        self::HOST . 'js/bootstrap/bootstrap.min.js',
        self::HOST . 'js/bootoast/bootoast.js',
        self::HOST . 'js/alert.js',
        self::HOST . 'js/myslideup.js',
        self::HOST . 'js/dropdown/dropdown.js',

        'html/h5_phone/app.js',
        ];
}