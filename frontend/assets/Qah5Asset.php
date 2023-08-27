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

    ];
    public $js = [

        ];


    public $depends = [
        'frontend\assets\AppAsset'
    ];
}