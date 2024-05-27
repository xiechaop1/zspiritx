<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'keyPrefix' => 'mus_'
        ],
        'html2pdf' => [
            'class' => 'yii2tech\html2pdf\Manager',
            'viewPath' => '@frontend/views/invoice',
            'converter' => [
                'class' => 'yii2tech\html2pdf\converters\Wkhtmltopdf',
                'defaultOptions' => [
                    'pageSize' => 'A4'
                ],
            ]
        ],
        'member' => 'common\services\Member',
        'category' => 'common\services\Category',
        'order' => 'common\services\Order',
        'verificationCode' => 'common\services\VerificationCode',
        'wechat'    => 'common\services\Wechat',
        'oplog'     => 'common\services\Log',
        'act'       => 'common\services\Actions',
        'knowledge' => 'common\services\Knowledge',
        'models'    => 'common\services\Models',
        'score'     => 'common\services\Score',
        'wechatPay'    => 'common\services\WechatPay',
        'lottery'   => 'common\services\Lottery',
        'baggage'   => 'common\services\Baggage',
        'storyRank'      => 'common\services\StoryRank',

        'location'      => 'common\services\Location',
        'userModels'      => 'common\services\UserModels',
        'qas'      => 'common\services\Qas',

        // add by xiechao
//        'ocr'               => 'common\services\Orc',
//        'AipOcr'            => 'aip-php-sdk-2.2.15/AipOcr.php',

        'upload'    => [
            'class' => 'common\services\Upload',
            'defaultDir' => '@frontend/web/documents/',
            'uploadShowDir' => '[URL]',
        ],
//        'curl'      => 'common\services\Curl',
    ],
];
