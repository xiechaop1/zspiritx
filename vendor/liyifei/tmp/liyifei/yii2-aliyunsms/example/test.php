<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2018/4/19
 * Time: 下午4:33
 */

require '../vendor/autoload.php';

require_once(dirname(__FILE__) . '/../vendor/yiisoft/yii2/Yii.php');
@(Yii::$app->charset = 'UTF-8');


$sms = new \liyifei\aliyunsms\Sms([
    'accessKeyId' => '',
    'accessKeySecret' => '',
    'signName' => '',
    'templateId' => '',
]);

var_dump($sms->sendSms('13616108550', ['code' => '123456']));