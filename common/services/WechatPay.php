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
    public $jsApiAppSecret;
//    const WECHAT_APP_ID = 'wxdc22108a3be1428d';
//    const WECHAT_APP_ID = 'wx15fe47d044ab1a36';   // test
//    const WECHAT_SECRET = 'c71f42740ff11f631691b3a73d374bc4';
//    const WECHAT_SECRET = '97e1f573c2ba3f75dbe88ffebddedf5a'; // test

    const MERCHANT_ID   = '';
    const MERCHANT_SERIAL_NUMBER = '';
    const MERCHANT_PRIVATE_KEY = '';
    const WECHATPAY_CERTIFICATE = '';

    const ORDER_TIMEOUT = 30;       // minutes

    private $_client;

    private $_token;

    public function createClient() {

        if ($this->_client) {
            return $this->_client;
        }

        // 商户相关配置，
        $merchantId = self::MERCHANT_ID; // 商户号
        $merchantSerialNumber = self::MERCHANT_SERIAL_NUMBER; // 商户API证书序列号
        $merchantPrivateKey = PemUtil::loadPrivateKey('./path/to/mch/private/key.pem'); // 商户私钥文件路径

        // 微信支付平台配置
        $wechatpayCertificate = PemUtil::loadCertificate('./path/to/wechatpay/cert.pem'); // 微信支付平台证书文件路径

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

    public function createH5Order($goodName, $outTradeNo, $amount) {
        $uri = '/v3/pay/transactions/h5';
        $host = 'https://api.mch.weixin.qq.com';
        $uri = $this->_createUri($uri, $host);

        $params = [
            // JSON请求体
            'json' => [
                "time_expire" => Date('Y-m-dTH:i:s+08:00', strtotime('+' . self::ORDER_TIMEOUT . 'mins')),
                // "2018-06-08T10:34:56+08:00",
                "amount" => [
                    "total" => $amount,
                    "currency" => "CNY",
                ],
                "mchid" => self::MERCHANT_ID,
                "description" => $goodName,
                "notify_url" => "https://www.zspiritx.com.cn/wechatpay/notify",
                "out_trade_no" => $outTradeNo,
                "appid" => $this->jsApiAppId,
//                "scene_info" => [
//                    "h5_info" => [
//                        "type" => "Wap",
//                        "wap_url" => "https://www.zspiritx.com.cn",
//                        "wap_name" => $goodName
//                    ]
//                ]
            ],
            'headers' => [ 'Accept' => 'application/json' ]
        ];

        $client = $this->createClient();

        try {
            $result = $this->_getPostApi($client, $uri, $params);
            return $result;
        } catch (RequestException $e) {
            return false;
        }
    }

    public function createJsapiOrder($code, $goodName, $outTradeNo, $amount) {
        $uri = '/v3/pay/transactions/jsapi';
        $host = 'https://api.mch.weixin.qq.com';
        $uri = $this->_createUri($uri, $host);

        $accessToken = Yii::$app->wechat->getAccessToken($code);

        if (!$accessToken) {
            return false;
        } else {
            $openId = $accessToken['openid'];
        }

        $params = [
            // JSON请求体
            'json' => [
                "time_expire" => Date('Y-m-dTH:i:s+08:00', strtotime('+' . self::ORDER_TIMEOUT . 'mins')),
                // "2018-06-08T10:34:56+08:00",
                "amount" => [
                    "total" => $amount * 100,
                    "currency" => "CNY",
                ],
                "mchid" => self::MERCHANT_ID,
                "description" => $goodName,
                "notify_url" => "https://www.zspiritx.com.cn/wechatpay/notify",
                "payer" => [
                    "openid" => $openId,
                ],
                "out_trade_no" => $outTradeNo,
                "appid" => $this->jsApiAppId,
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


        $ret = $this->_getPostApi($uri, $params);
        return $ret;

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

    private function _createJsApiOrderParams($params) {
        $ret = [
            // JSON请求体
            'json' => [
                "time_expire" => Date('Y-m-dTH:i:s+08:00', strtotime('+' . self::ORDER_TIMEOUT . 'mins')),
                    // "2018-06-08T10:34:56+08:00",
                "amount" => [
                    "total" => $params['amount']['total'],
                    "currency" => "CNY",
                ],
                "mchid" => self::MERCHANT_ID,
                "description" => $params['description'],
                "notify_url" => "https://www.zspiritx.com.cn/wechatpay/notify",
                "payer" => [
                    "openid" => "oUpF8uMuAJO_M2pxb1Q9zNjWeS6o",
                ],
                "out_trade_no" => $params['out_trade_no'],
//                "goods_tag" => "WXG",
                "appid" => $this->jsApiAppId,
//        "wxd678efh567hg6787",
                "attach" => $params['attach'],
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
        return $ret;
    }

    private function _getPostApi($uri, $postParams = []) {
        try {
            $resp = $this->_client->request(
                'POST',
                $uri,
                $postParams
            );
            $statusCode = $resp->getStatusCode();
            if ($statusCode == 200) { //处理成功
                echo "success,return body = " . $resp->getBody()->getContents()."\n";
            } else if ($statusCode == 204) { //处理成功，无返回Body
                echo "success";
            }
        } catch (RequestException $e) {
            // 进行错误处理
            echo $e->getMessage()."\n";
            if ($e->hasResponse()) {
                echo "failed,resp code = " . $e->getResponse()->getStatusCode() . " return body = " . $e->getResponse()->getBody() . "\n";
            }
            return;
        }
    }

}