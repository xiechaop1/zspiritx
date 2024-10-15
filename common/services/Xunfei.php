<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use WebSocket\Client;
use yii\base\Component;
use yii;

class Xunfei extends Component
{

    // define('DEMO_CURL_VERBOSE', false); // 打印curl debug信息


    const AMAP_HOST = 'https://restapi.amap.com';

    const DEMO_CURL_VERBOSE = false;

    public $appKey;
    public $appSecret;

    # 采样率
    const RATE = 16000;  // 固定值

    const END_TAG = '{"end": true}';


    private $_conn;

    /**
     * 拼接签名
     * @param $api_key
     * @param $api_secret
     * @param $time
     * @return string
     */
    private function sign($api_key, $api_secret, $time)
    {
        $signature_origin = 'host: ws-api.xfyun.cn' . "\n";
        $signature_origin .= 'date: ' . $time . "\n";
        $signature_origin .= 'GET /v2/iat HTTP/1.1';
        $signature_sha = hash_hmac('sha256', $signature_origin, $api_secret, true);
        $signature_sha = base64_encode($signature_sha);
        $authorization_origin = 'api_key="' . $api_key . '", algorithm="hmac-sha256", ';
        $authorization_origin .= 'headers="host date request-line", signature="' . $signature_sha . '"';
        $authorization = base64_encode($authorization_origin);
        return $authorization;
    }

    /**
     * 生成Url
     * @param $api_key
     * @param $api_secret
     * @return string
     */
    private function createUrl($api_key, $api_secret)
    {
        $url = 'wss://tts-api.xfyun.cn/v2/iat';
        $time = date('D, d M Y H:i:s', strtotime('-8 hour')) . ' GMT';
        $authorization = $this->sign($api_key, $api_secret, $time);
        $url .= '?' . 'authorization=' . $authorization . '&date=' . urlencode($time) . '&host=ws-api.xfyun.cn';
        return $url;
    }

    private function createConnection() {
        if (empty($this->_conn)) {
            $url = $this->createUrl($this->appKey, $this->appSecret);
//            $loop = Factory::create();
//            $this->_conn = new WsConnection($this->createUrl($this->appKey, $this->appSecret));
            $this->_conn = new Client($url);
        }
        return $this->_conn;
    }

    public function sendByFile($audioFile, $format = 'wav') {

        /** 拼接参数开始 **/
        $audio = file_get_contents($audioFile);

        // Todo: 增加一个过程，存一个临时文件，听听语音质量
        file_put_contents("/tmp/asr.wav", $audio);

        try {

            $connector = $this->createConnection();
            $connector->send($audio);

            usleep(50);
            $endTag = self::END_TAG;
            $connector->send($endTag);

            $rec = $connector->receive();
            $response = json_decode($rec, true);
        } catch (\Exception $e) {
            $response = [
                'errcode' => $e->getCode(),
                'errmsg' => $e->getMessage(),
            ];
        } finally {
            $connector->close();
        }

        return $response;
    }

    private function _createUri($uri, $host, $params = [], $needSign = false) {
        $uri = $host . $uri;

        if ($needSign) {
            ksort($params);
            $paramStrs = [];
            foreach ($params as $key => $val) {
                $paramStrs[] = $key . '=' . $val;
            }
            $paramStr = implode('&', $paramStrs);
            $signStr = md5($paramStr . $this->appSecret);

            $params['sig'] = $signStr;
        }

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

    private function _getPostApi($uri, $postParams = []) {
        $opts = [
            'CURLOPT_CONNECTTIMEOUT' => 5,
            'CURLOPT_TIMEOUT' => 60,
        ];

        $ret = Curl::curlPost($uri, $postParams, [], true, $opts);
        $ret = json_decode($ret, true);
        if (!empty($ret['errcode']) && $ret['errcode'] != 0) {
            throw new \Exception($ret['errmsg'], $ret['errcode']);
        }
        return $ret;
    }

}