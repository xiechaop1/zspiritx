<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/8/29
 * Time: 15:49
 */

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=music',
            'username' => 'root',
            'password' => '',
//            'password' => 'LXS1234qwer',
            'charset' => 'utf8mb4',
            'tablePrefix' => 'o_'
        ],
        'zhuge' => [
            'class' => 'common\extensions\da\zhuge\Service',
            'appKey' => 'cc42dc2363214e15aa8630aa147c001d',
            'appSecret' => 'fdb739bff6034ada916ed1beea158fa7',
        ],
        'wechat' => [
            'class' => 'common\services\Wechat',
            'appId' => 'wxdc22108a3be1428d',
            'appSecret' => 'c71f42740ff11f631691b3a73d374bc4',
        ],
        'getid3' => [
            'class' => 'common\extensions\getid3\Service',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
