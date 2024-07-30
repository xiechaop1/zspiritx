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
                'voice_type' => 'zh_female_linjianvhai_moon_bigtts',
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

    public function chatWithDoubao($userMessage, $oldMessages = [], $templateContents = array(), $roleTxts = array()) {

        if (empty($roleTxts)) {
            $roleTxt = '#角色' . "\n" . '你是一个教育方面的老师，你负责出题，解答和解析';
            $templateMessages[] = array('role' => 'system', 'content' => $roleTxt);
        } else {
            foreach ($roleTxts as $roleTxt) {
                $templateMessages[] = array('role' => 'system', 'content' => $roleTxt);
            }
        }
        $messages = array(
//            array('role' => 'system', 'content' => $roleTxt),
            array('role' => 'user', 'content' => $userMessage)
        );

//        $templateMessages = array();
        if (!empty($templateContents)) {
            foreach ($templateContents as $templateContent) {
                $templateMessages[] = array('role' => 'assistant', 'content' => $templateContent);
            }
        }

        if (!empty($oldMessages)) {
            array_unshift($oldMessages, ['role' => 'system', 'content' => '#历史消息']);
            $messages = array_merge($messages, $oldMessages);
        }
        $messages = array_merge($templateMessages, $messages);
        Yii::info('doubao messages: ' . json_encode($messages, JSON_UNESCAPED_UNICODE));
//        if (!empty($oldMessages)) {
//            var_dump($messages);exit;
//        }
//        var_dump($messages);
//        print_r($messages);
//        exit;

        $data = array(
//            'model' => 'ep-20240627053837-vs8wn',  // 或者使用其他模型
//            'model' => 'ep-20240628070258-6m88j',
            'model' => 'ep-20240729104951-snm9z',
            'messages' => $messages,
            'temperature' => 0.7,
//            'stream' => false,
        );

//        Yii::info('chatGPT data: ' . json_encode($data));

        $response = $this->_call('/v3/chat/completions', $data, 'POST');
//        if (!empty($oldMessages)) {
//            print_r($oldMessages);
//            print_r($messages);
//            print_r($response);
//        }
        Yii::info('doubao ret: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
//        file_put_contents('/tmp/doubao.tmp', json_encode($response, JSON_UNESCAPED_UNICODE));
//        var_dump($response);
//        exit;

        return json_decode($response, true);
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