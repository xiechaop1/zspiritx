<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 19-4-13
 * Time: 下午9:05
 */

require_once('../../vendor/autoload.php');

require_once(dirname(__FILE__) . '/../../vendor/yiisoft/yii2/Yii.php');
@(Yii::$app->charset = 'UTF-8');

$cJson = file_get_contents('config.txt');
$c = json_decode($cJson, true);

/**
 * @var ota\pkfare\Application $app
 */
$app = Yii::createObject([
    'class' => 'ota\pkfare\Application',
    'partnerId' => $c['partnerId'],
    'partnerKey' => $c['partnerKey'],
    'debug' => true
]);