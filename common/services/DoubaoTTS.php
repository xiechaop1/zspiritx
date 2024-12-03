<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\Subject;
use common\models\Qa;
use common\models\StoryMatch;
use common\models\UserExtends;
use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class DoubaoTTS extends Component
{

//    const HOST = 'https://api.openai.com/v1';
    const HOST = 'https://openspeech.bytedance.com';

    public $apiKey;

    public $appId;

    public $token;

    private $_token;

    public $clusterId;



    public function ttsWithDoubao($message, $userId = 0) {
        if (strpos($message, '：') !== false) {
            $res = Yii::$app->doubao->say2struct($message);
            var_dump($message);
            var_dump($res);
            exit;
        }
        $params = [
            'app' => [
                'appid' => $this->appId,
                'token' => $this->token,
                'cluster' => $this->clusterId,
            ],
            'user' => [
                'uid'   => $userId
            ],
            'audio' => [
//                'voice_type' => 'zh_female_linjianvhai_moon_bigtts',
                'voice_type' => 'BV051_streaming',
                'encoding' => 'mp3',
                'speed_ratio' => 1.0,
            ],
            'request' => [
                'reqid' => $userId . rand(10000, 99999),
                'text' => $message,
                'operation' => 'query',
            ],
        ];

        $response = $this->_call('/api/v1/tts', $params, 'POST');

        $response = json_decode($response, true);

        $ret = $response;

        if (!empty($response['data'])) {
            $fileInfo = $this->base642File($response['data'], $message);
            $ret = [
//                'res' => $response,
                'msg' => $message,
                'file' => $fileInfo,
            ];
            return $ret;
        }

        return $ret;
    }

    private function _call($interface, $params = array(), $method = 'POST') {
        $url = self::HOST . $interface;

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer;' . $this->token
        );

        if ($method == 'POST') {
            $response = Curl::curlPost($url, $params, $headers, true);
        } else {
            $response = Curl::curlGet($url);
        }
        Yii::info('doubao tts ret: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
//        file_put_contents('/tmp/tmp.tmp', $response);
//        var_dump($response);exit;

//        return json_decode($response, true);
        return $response;
    }

    public function base642File($base64, $text = '', $file = '') {
        $data = base64_decode($base64);

        if (empty($file)) {
            $file = '/tmp/' . md5($text) . '.mp3';
        }

        $saveFile = Yii::$app->basePath . '/web' . $file;

        file_put_contents($saveFile, $data);

        return ['file' => $file, 'saveFile' => $saveFile];
    }

//    public function text2Speech($text, $voice = 'nova') {
//        try {
//            $data = array(
//                'model' => 'tts-1',  // 或者使用其他模型
//                'input' => $text,
//                'voice' => $voice
//            );
//
//            $steam = $this->_call('/audio/speech', $data, 'POST');
//
//            $tempFile = '/tmp/' . md5($text) . '.mp3';
//
//            $saveFile = Yii::$app->basePath . '/web' . $tempFile;
//
//            file_put_contents($saveFile, $steam);
//
//            return $tempFile;
//        } catch (\Exception $e) {
//            throw $e;
//        }
//    }




}