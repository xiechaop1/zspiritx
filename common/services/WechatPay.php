<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;
use GuzzleHttp\Exception\RequestException;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use GuzzleHttp\HandlerStack;

class WechatPay extends Component
{

    const WECHAT_HOST = 'https://api.weixin.qq.com';

    public $appId;
    public $appSecret;

    public $jsApiAppId;
    public $tdJsApiAppId;
    public $jsApiAppSecret;
//    const WECHAT_APP_ID = 'wxdc22108a3be1428d';
//    const WECHAT_APP_ID = 'wx15fe47d044ab1a36';   // test
//    const WECHAT_SECRET = 'c71f42740ff11f631691b3a73d374bc4';
//    const WECHAT_SECRET = '97e1f573c2ba3f75dbe88ffebddedf5a'; // test

    public $merchant;

    const MERCHANT_ID   = '1667566912';
    const MERCHANT_SERIAL_NUMBER = '4BAA56AF30CFEA2D70B90A1AF8F111F3021F1A82';
    const MERCHANT_PRIVATE_KEY = '';
    const WECHATPAY_CERTIFICATE = '';


    const ORDER_TIMEOUT = 30;       // minutes

    private $_client;

    private $_token;

    private $_appId;

    private $_mch;

    // 解密微信回调数据
    public function decryptData($data) {
        $iv = Yii::$app->params['wechat']['iv'];
        $sessionKey = Yii::$app->params['wechat']['sessionKey'];
        $appid = Yii::$app->params['wechat']['appid'];
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($data['encryptedData'], $iv, $data);
        if ($errCode == 0) {
            return $data;
        } else {
            return false;
        }
    }

    public function setMch($channel) {
        $mch = $this->_getMch($channel);

        $this->_mch = $mch;

        $this->_appId = $this->jsApiAppId;
        if (!empty($channel)) {
            switch ($channel) {
                case 'gyj':
                    $this->_appId = $this->tdJsApiAppId['gyj'];
                    break;
                default:
                    $this->_appId = $this->jsApiAppId;
                    break;
            }
        }
    }

    public function createClient($channel) {

        if ($this->_client) {
            return $this->_client;
        }

        $merchantId = self::MERCHANT_ID; // 商户号
        $merchantSerialNumber = self::MERCHANT_SERIAL_NUMBER; // 商户API证书序列号

        $mch = $this->_getMch($channel);
        $merchantId = $mch['merchantId'];
        $merchantSerialNumber = $mch['merchantSerialNumber'];
        $prefix = $mch['prefix'];
        $pltpem = $mch['pltpem'];

//        if (!empty($channel)) {
//            if (!empty($this->merchant[$channel])) {
//                $merchantId = !empty($this->merchant[$channel]['id']) ? $this->merchant[$channel]['id'] : '';
//                $merchantSerialNumber = !empty($this->merchant[$channel]['serialNumber']) ? $this->merchant[$channel]['serialNumber'] : '';
//                $prefix = !empty($this->merchant[$channel]['prefix']) ? $this->merchant[$channel]['prefix'] : '';
//            } else {
//                $channel = 'default';
//                $merchantId = !empty($this->merchant[$channel]['id']) ? $this->merchant[$channel]['id'] : '';
//                $merchantSerialNumber = !empty($this->merchant[$channel]['serialNumber']) ? $this->merchant[$channel]['serialNumber'] : '';
//                $prefix = !empty($this->merchant[$channel]['prefix']) ? $this->merchant[$channel]['prefix'] : '';
//            }
//        } else {
//            $channel = 'default';
//            $merchantId = !empty($this->merchant[$channel]['id']) ? $this->merchant[$channel]['id'] : '';
//            $merchantSerialNumber = !empty($this->merchant[$channel]['serialNumber']) ? $this->merchant[$channel]['serialNumber'] : '';
//            $prefix = !empty($this->merchant[$channel]['prefix']) ? $this->merchant[$channel]['prefix'] : '';
//        }

        // 商户相关配置，
//        $merchantId = self::MERCHANT_ID; // 商户号
//        $merchantSerialNumber = self::MERCHANT_SERIAL_NUMBER; // 商户API证书序列号
        $merchantPrivateKey = PemUtil::loadPrivateKey( dirname(__FILE__) . '/../../frontend/web/cert/' . $prefix . '/apiclient_key.pem'); // 商户私钥文件路径

        // 微信支付平台配置
//        $wechatpayCertificate = PemUtil::loadCertificate(dirname(__FILE__) . '/../../frontend/web/cert/' . $prefix . '/apiclient_cert.pem'); // 微信支付平台证书文件路径
        $wechatpayCertificate = PemUtil::loadCertificate(dirname(__FILE__) . '/../../frontend/web/cert/' . $prefix . '/' . $pltpem); // 微信支付平台证书文件路径
        // 构造一个WechatPayMiddleware
        $wechatpayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
            ->withWechatPay([ $wechatpayCertificate ]) // 可传入多个微信支付平台证书，参数类型为array
            ->build();

        // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->push($wechatpayMiddleware, 'wechatpay');

        // 创建Guzzle HTTP Client时，将HandlerStack传入，接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        $this->_client = new \GuzzleHttp\Client(['handler' => $stack]);

        return $this->_client;
    }

    // $goodName, $outTradeNo, $amount
    public function createH5Order($story, $order, $userInfo = [], $channel = '') {

        $this->setMch($channel);

        $uri = '/v3/pay/transactions/h5';
        $host = 'https://api.mch.weixin.qq.com';
        $uri = $this->_createUri($uri, $host);

        $storyTitle = !empty($story->title) ? $story->title : '未知故事';
        $outTradeNo = !empty($order->order_no) ? $order->order_no : \common\helpers\Order::generateOutTradeNo($userInfo, $story->id, $order->pay_method);
        $amount = !empty($order->amount) ? $order->amount : 0;

        $this->_appId = $appId = $this->jsApiAppId;
        if (!empty($channel)) {
            switch ($channel) {
                case 'gyj':
                    $this->_appId = $appId = $this->tdJsApiAppId['gyj'];
                    break;
                default:
                    $this->_appId = $appId = $this->jsApiAppId;
                    break;
            }
        }

        $mch = $this->_getMch($channel);
        $merchantId = $mch['merchantId'];
        $merchantSerialNumber = $mch['merchantSerialNumber'];
        $prefix = $mch['prefix'];

        $params = [
            // JSON请求体
            'json' => [
                "time_expire" => Date('Y-m-d\TH:i:s+08:00', strtotime('+' . self::ORDER_TIMEOUT . 'mins')),
                // "2018-06-08T10:34:56+08:00",
                "amount" => [
                    "total" => round($amount * 100),
                    "currency" => "CNY",
                ],
                "mchid" => $merchantId,
                "description" => $storyTitle,
                "notify_url" => "https://www.zspiritx.com.cn/wechatpay/notify",
                "out_trade_no" => $outTradeNo,
                "appid" => $appId,
                "scene_info" => [
                    "h5_info" => [
                        "type" => "H5",
                        "wap_url" => "https://www.zspiritx.com.cn",
                        "wap_name" => $storyTitle
                    ]
                ]
            ],
            'headers' => [ 'Accept' => 'application/json' ]
        ];

        $client = $this->createClient($channel);

        try {
            $result = $this->_getPostApi($uri, $params, $channel);
            return $result;
        } catch (RequestException $e) {
            throw $e;
        }
    }

    public function createJsapiOrder($code, $story, $order, $userInfo = [], $channel = '') {
        $this->setMch($channel);

        $uri = '/v3/pay/transactions/jsapi';
        $host = 'https://api.mch.weixin.qq.com';
        $uri = $this->_createUri($uri, $host);

//        $accessToken = Yii::$app->wechat->getAccessToken($code);
        $this->_appId = $appId = $this->jsApiAppId;
        if (!empty($channel)) {
            switch ($channel) {
                case 'gyj':
                    $this->_appId = $appId = $this->tdJsApiAppId['gyj'];
                    break;
                default:
                    $this->_appId = $appId = $this->jsApiAppId;
                    break;
            }
        }

//        $openId = 'oDeH-7E3_p4ToG65qqOKT1WoWdpA';

        $accessSession = Yii::$app->wechat->getSession($code, $channel);

        if (!$accessSession) {
            return false;
        } else {
            $openId = $accessSession['openid'];
        }

//        var_dump($this->getCertificates($channel));exit;

        $storyTitle = !empty($story->title) ? $story->title : '未知故事';
        $outTradeNo = !empty($order->order_no) ? $order->order_no : \common\helpers\Order::generateOutTradeNo($userInfo, $story->id, $order->pay_method);
        $outTradeNo = substr($outTradeNo, 0, 32);
        $amount = !empty($order->amount) ? $order->amount : 0;

        $mch = $this->_getMch($channel);
        $merchantId = $mch['merchantId'];
        $merchantSerialNumber = $mch['merchantSerialNumber'];
        $prefix = $mch['prefix'];

        $params = [
            // JSON请求体
            'json' => [
                "time_expire" => Date('Y-m-d\TH:i:s+08:00', strtotime('+' . self::ORDER_TIMEOUT . 'mins')),
                // "2018-06-08T10:34:56+08:00",
                "amount" => [
                    "total" => round($amount * 100),
                    "currency" => "CNY",
                ],
                "mchid" => $merchantId,
                "description" => $storyTitle,
                "notify_url" => "https://www.zspiritx.com.cn/wechatpay/notify",
                "payer" => [
                    "openid" => $openId,
                ],
                "out_trade_no" => $outTradeNo,
                "appid" => $appId,
//        "wxd678efh567hg6787",
                "attach" => '创建订单' . time(),
//                "detail" => [
//                    "invoice_id" => "wx123",
//                    "goods_detail" => [
//                        [
//                            "goods_name" => "iPhoneX 256G",
//                            "wechatpay_goods_id" => "1001",
//                            "quantity" => 1,
//                            "merchant_goods_id" => "商品编码",
//                            "unit_price" => 828800,
//                        ],
//                        [
//                            "goods_name" => "iPhoneX 256G",
//                            "wechatpay_goods_id" => "1001",
//                            "quantity" => 1,
//                            "merchant_goods_id" => "商品编码",
//                            "unit_price" => 828800,
//                        ],
//                    ],
//                    "cost_price" => 608800,
//                ],
//                "scene_info" => [
//                    "store_info" => [
//                        "address" => "广东省深圳市南山区科技中一道10000号",
//                        "area_code" => "440305",
//                        "name" => "腾讯大厦分店",
//                        "id" => "0001",
//                    ],
//                    "device_id" => "013467007045764",
//                    "payer_client_ip" => "14.23.150.211",
//                ]
            ],
            'headers' => [ 'Accept' => 'application/json' ]
        ];

        $client = $this->createClient($channel);

        $tmpRet = $this->_getPostApi($uri, $params, $channel);

        $tmpJson = json_decode($tmpRet, true);

        $ret = $this->generateSign($tmpJson['prepay_id'], $this->_appId, $prefix);

        return $ret;

    }

    public function createRefundOrderByOrder($order, $channel = '') {
        $this->setMch($channel);

        $uri = '/v3/refund/domestic/refunds';
        $host = 'https://api.mch.weixin.qq.com';
        $uri = $this->_createUri($uri, $host);

        $mch = $this->_getMch($channel);
        $merchantId = $mch['merchantId'];
        $merchantSerialNumber = $mch['merchantSerialNumber'];
        $prefix = $mch['prefix'];

        $order->refund_no = !empty($order->refund_no)
            ? $order->refund_no
            : \common\helpers\Order::generateOutTradeNo($order->userId, $order->storyId, $order->pay_method, 'ZR');

        $order->save();

        $params = [
            // JSON请求体
            'json' => [
                "amount" => [
                    "refund" => round($order->amount * 100),
                    "total" => round($order->amount * 100),
                    "currency" => "CNY",
                ],
                "out_trade_no" => $order->order_no,
                "out_refund_no" => $order->refund_no,
                "reason" => "用户退款：" . !empty($order->story->story_name) ? $order->story->story_name : '未知剧本',
                "notify_url" => "https://www.zspiritx.com.cn/wechatpay/refund_notify",
            ],
            'headers' => [ 'Accept' => 'application/json' ]
        ];

        $client = $this->createClient($channel);

        $tmpRet = $this->_getPostApi($uri, $params, $channel);

        return $tmpRet;
    }

    private function _createUri($uri, $host, $params = []) {
        $uri = $host . $uri;
        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }
        return $uri;
    }

    private function _getApi($uri) {
        $ret = Curl::curlGet($uri);
        $ret = json_decode($ret, true);
        if (!empty($ret['errcode']) && $ret['errcode'] != 0) {
            throw new \Exception($ret['errmsg'], $ret['errcode']);
        }
        return $ret;
    }


    private function _getPostApi($uri, $postParams = [], $channel = '') {
        try {
            $mch = $this->_getMch($channel);
            $merchantId = $mch['merchantId'];
            $merchantSerialNumber = $mch['merchantSerialNumber'];
            $prefix = $mch['prefix'];

//            $token = $this->_createAuth($uri, 'POST', $postParams, $merchantId, $merchantSerialNumber, $prefix);
            $resp = $this->_client->request(
                'POST',
                $uri,
                $postParams
//                [
//                    'header' => [
//                        'Authorization' => $token,
//                        'Content-Type' => 'application/json',
//                    ],
//                    'body' => $postParams,
//                ]
            );
            $statusCode = $resp->getStatusCode();
            if ($statusCode == 200) { //处理成功
                $tmpRet = $resp->getBody()->getContents();

                return $tmpRet;
//                echo "success,return body = " . $resp->getBody()->getContents()."\n";
            } else if ($statusCode == 204) { //处理成功，无返回Body
                return true;
//                echo "success";
            }
        } catch (RequestException $e) {
            var_dump($e);exit;
            throw $e;
            // 进行错误处理
//            echo $e->getMessage()."\n";
//            if ($e->hasResponse()) {
//                echo "failed,resp code = " . $e->getResponse()->getStatusCode() . " return body = " . $e->getResponse()->getBody() . "\n";
//            }
//            return;
        }
    }

    // 生成微信支付JsApi v3的签名
    private function _createAuth($url, $http_method = 'POST', $body, $merchantId, $merchantSerialNumber, $prefix = '') {

        $body = json_encode($body);

//        $mch = $this->_getMch($channel);
//        $merchantId = $mch['merchantId'];
//        $merchantSerialNumber = $mch['merchantSerialNumber'];
//
//        $prefix = !empty($mch['prefix']) ? $mch['prefix'] : '';

        $keyFile = openssl_get_privatekey(file_get_contents(
            dirname(__FILE__) . '/../../frontend/web/cert/' . $prefix . '/apiclient_key.pem'
        ));

        $timestamp = time();
        $nonce = uniqid();
//        $body = '';
//        $http_method = 'POST';
//        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';
//        $url = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI'];
        $url_parts = parse_url($url);
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        $message = $http_method."\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";

        openssl_sign($message, $raw_sign, $keyFile, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);

        $schema = 'WECHATPAY2-SHA256-RSA2048';
        $token = sprintf('%s mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $schema, $merchantId, $nonce, $timestamp, $merchantSerialNumber, $sign);

        return $token;

    }

    public function generateSign($packageStr, $appId, $prefix) {

        $timeStamp = time();
        $nonceStr = uniqid();

        $package = 'prepay_id=' . $packageStr;

        $message = $appId."\n".
            $timeStamp."\n".
            $nonceStr."\n".
            $package."\n";

        $keyFile = openssl_get_privatekey(file_get_contents(
            dirname(__FILE__) . '/../../frontend/web/cert/' . $prefix . '/apiclient_key.pem'
        ));

        openssl_sign($message, $raw_sign, $keyFile, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);

        return [
            'appId' => $appId,
            'timeStamp' => $timeStamp,
            'nonceStr' => $nonceStr,
            'package' => $package,
            'signType' => 'RSA',
            'paySign' => $sign,
        ];
    }

    private function _getMch($channel) {
        if (!empty($channel)) {
            if (!empty($this->merchant[$channel])) {
                $merchantId = !empty($this->merchant[$channel]['id']) ? $this->merchant[$channel]['id'] : '';
                $merchantSerialNumber = !empty($this->merchant[$channel]['serialNumber']) ? $this->merchant[$channel]['serialNumber'] : '';
                $prefix = !empty($this->merchant[$channel]['prefix']) ? $this->merchant[$channel]['prefix'] : '';
                $pltpem = !empty($this->merchant[$channel]['pltpem']) ? $this->merchant[$channel]['pltpem'] : '';
            } else {
                $channel = 'default';
                $merchantId = !empty($this->merchant[$channel]['id']) ? $this->merchant[$channel]['id'] : '';
                $merchantSerialNumber = !empty($this->merchant[$channel]['serialNumber']) ? $this->merchant[$channel]['serialNumber'] : '';
                $prefix = !empty($this->merchant[$channel]['prefix']) ? $this->merchant[$channel]['prefix'] : '';
                $pltpem = !empty($this->merchant[$channel]['pltpem']) ? $this->merchant[$channel]['pltpem'] : '';
            }
        } else {
            $channel = 'default';
            $merchantId = !empty($this->merchant[$channel]['id']) ? $this->merchant[$channel]['id'] : '';
            $merchantSerialNumber = !empty($this->merchant[$channel]['serialNumber']) ? $this->merchant[$channel]['serialNumber'] : '';
            $prefix = !empty($this->merchant[$channel]['prefix']) ? $this->merchant[$channel]['prefix'] : '';
            $pltpem = !empty($this->merchant[$channel]['pltpem']) ? $this->merchant[$channel]['pltpem'] : '';
        }

        return [
            'merchantId' => $merchantId,
            'merchantSerialNumber' => $merchantSerialNumber,
            'prefix' => $prefix,
            'pltpem' => $pltpem,
        ];
    }


}