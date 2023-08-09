<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/15
 * Time: 4:35 PM
 */

require 'common.php';


$data = new \ota\pkfare\libs\params\OrderPricing([
    'orderNum' => '1024976534'
]);

$driver = $app->driver('order-pricing');

try {
    $result = $driver->request($data);
    print_r($result);
} catch (\yii\base\Exception $e) {
    var_dump($driver->lastErrorCode);
    var_dump($e->getMessage());
}
