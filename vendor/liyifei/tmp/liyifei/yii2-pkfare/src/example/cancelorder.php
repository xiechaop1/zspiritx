<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 9:26 AM
 */


require 'common.php';



$data = new \ota\pkfare\libs\params\Cancel([
    'orderNum' => '1024975580',
    'virtualPnr' => 'SWNCEV'
]);

$driver = $app->driver('cancel-order');

print_r($driver->request($data));