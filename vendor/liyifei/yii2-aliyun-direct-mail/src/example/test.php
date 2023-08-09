<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/5/15
 * Time: 下午2:45
 */

require '../../vendor/autoload.php';

$client = new \liyifei\DirectMail\Mail(
    '','','','',''
);

print_r($client);

$client->send('119422342@qq.com', 'test', 'hello world');