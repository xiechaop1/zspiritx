<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/18
 * Time: 下午2:14
 */

require 'vendor/autoload.php';

require_once('vendor/yiisoft/yii2/Yii.php');
@(Yii::$app->charset = 'UTF-8');

/**
 * @var liyifei\googleapi\GoogleApiLibrary $app
 */
$app = Yii::createObject([
    'class' => 'liyifei\googleapi\GoogleApiLibrary',
    'geocodeApiUrl' => 'https://maps.google.cn',
    'geocode_api_key' => '',
]);


print_r($app->getGeoCodeObject('江苏省常州市戚墅堰'));