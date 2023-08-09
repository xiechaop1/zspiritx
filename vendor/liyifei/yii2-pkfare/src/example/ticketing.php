<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/15
 * Time: 4:35 PM
 */

require 'common.php';


$data = new \ota\pkfare\libs\params\Ticketing([
    'orderNum' => '1024976534',
    'PNR' => 'XRZJLS',
    'name' => 'liyifei',
    'email' => '119422342@qq.com',
    'telNum' => '+8613616108550'
]);

$driver = $app->driver('ticketing');

try {
    $result = $driver->request($data);
    print_r($result);
} catch (\yii\base\Exception $e) {
    var_dump($driver->lastErrorCode);
    var_dump($e->getMessage());
}
