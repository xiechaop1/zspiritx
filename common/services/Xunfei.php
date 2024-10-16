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


    public $appKey;
    public $appSecret;
    public $appId;

    # 采样率
    const RATE = 16000;  // 固定值

    const END_TAG = '{"end": true}';

    const REAL_WEBAPI = 'wss://rtasr.xfyun.cn/v1/ws';

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
        $signature_origin = 'host: iat-api.xfyun.cn' . "\n";
        $signature_origin .= 'date: ' . $time . "\n";
        $signature_origin .= 'GET /v2/iat HTTP/1.1';
        $signature_sha = hash_hmac('sha256', $signature_origin, $api_secret, true);
        $signature_sha = base64_encode($signature_sha);
        $authorization_origin = 'api_key="' . $api_key . '", algorithm="hmac-sha256", ';
        $authorization_origin .= 'headers="host date request-line", signature="' . $signature_sha . '"';
        $authorization = base64_encode($authorization_origin);
        $authorization = urlencode($authorization);
        return $authorization;
    }

    private function realSign($api_key, $api_secret, $time)
    {
        $baseString = $api_key . $time;
        $baseString = md5($baseString);
        $signature = base64_encode(hash_hmac('sha1', $baseString, $api_secret, true));

        return $signature;
    }

    /**
     * 生成Url
     * @param $api_key
     * @param $api_secret
     * @return string
     */
    private function createUrl($api_key, $api_secret)
    {
        $url = 'wss://iat-api.xfyun.cn/v2/iat';
        $time = date('D, d M Y H:i:s', strtotime('-8 hour')) . ' GMT';
        $authorization = $this->sign($api_key, $api_secret, $time);
        $url .= '?' . 'authorization=' . $authorization . '&date=' . urlencode($time) . '&host=iat-api.xfyun.cn';
        return $url;
    }

    private function createRealUrl($api_key, $api_secret)
    {
        $time = time();
        $signature = $this->realSign($this->appId, $api_key, $time);

        $parameters = [
            'appid' => $this->appId,
            'ts' => $time,
            'signa' => $signature,
            'punc' => 1,    // 不过滤标点
        ];

        $para = http_build_query($parameters);

        $url = self::REAL_WEBAPI . '?' . $para;
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

    private function createRealConnection() {
        if (empty($this->_conn)) {
            $url = $this->createRealUrl($this->appKey, $this->appSecret);
            $this->_conn = new Client($url);
        }
        return $this->_conn;
    }

    public function sendRealByFile($audioFile) {
        $audioHandler = fopen($audioFile, 'r');

        $ct = (int)(filesize($audioFile) / 1280) + 1;
        $connector = $this->createRealConnection();

        file_put_contents('/tmp/xunfei.wav', file_get_contents($audioFile));
        file_put_contents('/tmp/xunfei_au.log', Date('Y-m-d H:i:s') . "\n");
        `exec rm /tmp/xunfei1.wav'`;
        try {
            for ($i = 0; $i < $ct; $i++) {
                $audio = fread($audioHandler, 1280);
                file_put_contents('/tmp/xunfei_au.log', 'before:' . $i . ' ' . strlen($audio) . "\n", FILE_APPEND);

                file_put_contents('/tmp/xunfei1.wav', $audio, FILE_APPEND);

                if ($audio === false) {
                    break;
                }

                if (empty($audio)) {
                    break;
                }

                file_put_contents('/tmp/xunfei_au.log', 'after:' . $i . ' ' . strlen($audio) . "\n", FILE_APPEND);

                $connector->setFragmentSize(1280);
                $connector->send($audio);

                usleep(40);
            }
            fclose($audioHandler);

            $endTag = self::END_TAG;
            $connector->send($endTag);

            $ret = '';
            while (true) {
                $rec = $connector->receive();
                $response = json_decode($rec, true);

                file_put_contents('/tmp/xunfei_real.log', print_r($response, true));

                if (empty($response)) {
                    break;
                }

                if ($response['action'] == 'error' || $response['code'] != 0) {
                    file_put_contents('/tmp/xunfei_rec.log', print_r($response, true) . "\n");
                    throw new \Exception($response['desc'], $response['code']);
                }

                $dataJson = $response['data'];
                $data = json_decode($dataJson, true);
                $rt = $data['cn']['st']['rt'];
                if (!empty($rt) && is_array($rt)) {
                    foreach ($rt as $r) {
                        $ret .= $r['ws']['cw'][0]['w'];
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $connector->close();
        }
//        $connector->close();

        return $ret;
    }

    public function sendByFile($audioFile, $format = 'wav') {

        /** 拼接参数开始 **/
        $audio = file_get_contents($audioFile);

        // Todo: 增加一个过程，存一个临时文件，听听语音质量
        file_put_contents("/tmp/asr.wav", $audio);

        try {

            $params = [
                'common' => [
                    'app_id' => $this->appId,
                ],
                'business' => [
                    'language' => 'zh_cn',
                    'domain' => 'iat',
                    'accent' => 'mandarin',
                    'vad_eos' => 5000,
                    'dwa' => 'wpgs',
                    'nunum' => 1,
                    'aue' => 'raw',
//                    'result_level' => 'plain',
//                    'sample_rate' => self::RATE,
                ],
                'data' => [
                    'status' => 0,
                    'format' => 'audio/L16;rate=16000',
                    'encoding' => 'raw',
                    'audio' => base64_encode($audio),
                ],
            ];

            $connector = $this->createConnection();
            $connector->setFragmentSize(1280);
            $connector->send(json_encode($params, true));

            usleep(50);
            $endTag = self::END_TAG;
            $connector->send($endTag);

            $ret = '';
            while (true) {
                $rec = $connector->receive();
                $response = json_decode($rec, true);

                if (empty($response) || empty($response['data']['status'])) {
                    break;
                }

                $ws = $response['data']['result']['ws'];
                if (!empty($ws) && is_array($ws)) {
                    foreach ($ws as $w) {
                        $ret .= $w['cw'][0]['w'];
                    }
                }


                if ($response['data']['status'] == 2) {
                    break;
                }
            }


        } catch (\Exception $e) {
//            $response = [
//                'errcode' => $e->getCode(),
//                'errmsg' => $e->getMessage(),
//            ];
            throw $e;
        } finally {
            $connector->close();
        }

        return $ret;
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