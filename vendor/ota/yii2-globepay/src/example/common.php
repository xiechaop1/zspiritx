<?php
/**
 * Created by PhpStorm.
 * User: yifei
 * Date: 2019/3/18
 * Time: 22:48
 */
require_once('../../vendor/autoload.php');

require_once(dirname(__FILE__) . '/../../vendor/yiisoft/yii2/Yii.php');
@(Yii::$app->charset = 'UTF-8');

$cJson = file_get_contents('config.txt');
$c = json_decode($cJson, true);

/**
 * @var ota\globepay\Application $app
 */
$app = Yii::createObject([
    'class' => 'ota\globepay\Application',
    'partnerCode' => $c['p'],
    'credentialCode' => $c['c']
]);