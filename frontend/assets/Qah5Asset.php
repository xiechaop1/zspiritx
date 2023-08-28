<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 2:12 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class Qah5Asset extends AssetBundle
{
    public $sourcePath = '@runtime/../../template';

    public $css = [
    	'css/bootstrap/bootstrap.min.css',
        'js/bootoast/bootoast.css',
        'css/theme.css',
        'css/dropdown/dropdowm.css',
        'css/iconfont.css',
        'css/datePicker/bootstrap-datepicker.css',
        'css/bootstrap-clockpicker.css',
        'js/jqueryToast/css/toast.styleH5.css',
        'css/iconfont.css',
        'css/animate.css',
        'html/h5/app.css',
        'html/h5/qa.css'

    ];
    public $js = [
    	'js/jquery/jQuery-2.1.3.min.js',
        'js/jquery/jquery.json.min.js',
        'js/Popper/Popper.js',
        'js/bootstrap/bootstrap.min.js',
        'js/bootoast/bootoast.js',
        'html/h5/app.js',
        'js/alert.js',
        'js/myslideup.js',
        'js/dropdown/dropdown.js',
        'js/datePicker/moment.js',
        'js/jqueryToast/js/toast.script.js',
        'js/getOptions.js',
        'js/jquery.SuperSlide.2.1.3.js',

        'html/h5/qa.js',
        ];


}