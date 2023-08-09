<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Asia/Shanghai',
    'language' => 'zh-CN',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'frontend\models\MemberIdentity',
            'enableAutoLogin' => true,
            'enableSession' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['/site/index'],
        ],
        'i18n' => [
            'translations' => [
                'web' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@frontend/messages',
                ]
            ]
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'assetManager' => [
            'linkAssets' => true,
            'forceCopy' => false,
            'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => '@runtime/../../template',
                    'js' => [
                        'js/jquery/jquery.js'
                    ]
                ]
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 2,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logVars' => ['_POST', '_GET']
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'homepage' => 'site/index',
                'feedback' => 'site/feedback',
                'joinus' => 'site/joinus',
                'contact' => 'site/contact',
                'service-manual' => 'site/service-manual',
                'cookie' => 'site/cookie',
                'privacy' => 'site/privacy',
                'account/notice/<id:\d+>' => 'account/notices'
            ],
        ],
    ],
    'on beforeAction' => function ($action) {
        /**
         * @var \yii\web\View $view
         */
        $view = $action->action->controller->getView();
        if ($view && $view instanceof \yii\web\View) {
            $view->registerJs(new \yii\web\JsExpression('var mobile_sections = ' . json_encode(Yii::$app->params['mobile.section']) . ';'), \yii\web\View::POS_HEAD);

            if (!Yii::$app->user->isGuest) {
                $view->registerJs(new \yii\web\JsExpression('var uniqueid="' . md5(Yii::$app->user->id) . '";'), \yii\web\View::POS_HEAD);
            }
        }

        if (Yii::$app->request->isGet && !Yii::$app->user->isGuest) {
            Yii::$app->user->identity->last_visit = time();
            Yii::$app->user->identity->save();
        }

//        Yii::$app->order->handleExpired();

    },
    'params' => $params,
];
