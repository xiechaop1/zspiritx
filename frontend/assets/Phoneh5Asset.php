<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 2:12 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class phoneh5Asset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    CONST HOST = 'https://s.nowkey.net/zspiritx/';
    public $css = [
        'html/h5_keypad/keypad.css',
    ];
    public $js = [
        'html/h5_keypad/jquery.js',
         self::HOST . 'js/jquery/jquery.json.min.js',
        'html/h5_keypad/keypad.js',
        ];
}