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
        file_put_contents('/tmp/tts.log', $message . PHP_EOL);
        $voiceTypeLists = [
            'BV063_streaming',
            'BV417_streaming',
            'BV050_streaming',
            'BV061_streaming',
        ];
        $msgLists = [];
        if (strpos($message, '“') !== false) {
            $res = Yii::$app->doubao->say2struct($message);
            file_put_contents('/tmp/tts.log', print_r($res, true) , FILE_APPEND);

            $tmpMsg = $message;
            $start = 0;
            if (!empty($res) && is_array($res)) {
                foreach ($res as $sayOne) {
                    if (is_array($sayOne)) {
                        $role = !empty($sayOne['role']) ? $sayOne['role'] : '';
                        $text = !empty($sayOne['text']) ? $sayOne['text'] : '';

                        if (!empty($text)) {
                            $txtPos = mb_strpos($tmpMsg, $text, 0, 'UTF-8');
                            $msgLists[] = [
                                'role' => 0,
                                'text' => mb_substr($tmpMsg, $start, $txtPos, 'UTF-8'),
                            ];
                            $msgLists[] = [
                                'role' => $role,
                                'text' => $text,
                            ];
                            $tmpMsg = mb_substr($tmpMsg, mb_strlen($text, 'UTF-8') + $txtPos, mb_strlen($tmpMsg, 'UTF-8'), 'UTF-8');
                        }
                    }
                }
            }
            $msgLists[] = [
                'role' => 0,
                'text' => $tmpMsg,
            ];
            file_put_contents('/tmp/tts.log', print_r($msgLists, true) , FILE_APPEND);

//            var_dump($res);
//            var_dump($message);
            exit;
        } else {
            $msgLists[] = [
                'role' => 0,
                'text' => $message,
            ];
        }
//        $ret = [
////                'res' => $response,
//            'msg' => $message,
//            'file' => '',
//        ];
//        return $ret;

        $tmpData = '';
        $voiceType = 'BV051_streaming';
        $roleVoice = [];
        if (!empty($msgLists)) {
            foreach ($msgLists as $msgList) {
                $role = !empty($msgList['role']) ? $msgList['role'] : 0;
                if ($role == 0) {
                    $voiceType = 'BV051_streaming';
                } else {
                    if (!empty($roleVoice[$role])) {
                        $voiceType = $roleVoice[$role];
                    } else {
                        $voiceType = array_shift($voiceTypeLists);
                        $roleVoice[$role] = $voiceType;
                    }
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
                        'voice_type' => $voiceType,
                        'encoding' => 'mp3',
                        'speed_ratio' => 1.0,
                    ],
                    'request' => [
                        'reqid' => $userId . rand(10000, 99999),
                        'text' => $msgList['text'],
                        'operation' => 'query',
                    ],
                ];

                $response = $this->_call('/api/v1/tts', $params, 'POST');

                $response = json_decode($response, true);

                $ret = $response;

                if (!empty($response['data'])) {
                    $tmpData .= $response['data'];
                }
            }
        }
        $ret = [];
        if (!empty($tmpData)) {
            $fileInfo = $this->base642File($tmpData, $message);
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