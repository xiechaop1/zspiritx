<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 2:12 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class Payh5Asset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    CONST HOST = 'https://file.zspiritx.com.cn/static/';
    public $css = [
        'html/h5_keypad/keypad.css',
    ];
    public $js = [
        'html/h5_keypad/jquery.js',
         self::HOST . 'js/jquery/jquery.json.min.js',
//          'http://res.wx.qq.com/open/js/jweixin-1.4.0.js',
        'html/h5_keypad/pay.js',
        ];
}