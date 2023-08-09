<?php
/**
 * Created by PhpStorm.
 * User: yifei
 * Date: 2019/3/18
 * Time: 22:54
 */

require 'common.php';

//$input->setOrderId(GlobePayConfig::PARTNER_CODE . date("YmdHis"));
//$input->setDescription("test");
//$input->setPrice("1");
//$input->setCurrency("GBP");
//$input->setNotifyUrl("http://115.29.162.214/example/notify.php");
//$input->setOperator("123456");

print_r($app->driver('unifiedorder', [
    'orderId' => 'SN' . date('YmdHis',time()),
    'description' => 'test',
    'price' => 2,
    'currency' => 'GBP',
    'notifyUrl' => 'http://115.29.162.214/example/notify.php',
    'operator' => '123123'
])->qrOrder());