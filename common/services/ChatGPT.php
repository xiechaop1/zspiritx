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

class ChatGPT extends Component
{

//    const CHATGPT_HOST = 'https://api.openai.com/v1';
    const CHATGPT_HOST = 'https://openai-proxy-openai-proxy-fbgfvcgtgs.us-west-1.fcapp.run/v1';

    public $apiKey;

    private $_token;

    public function callOpenAIChatGPT($userMessage, $templateContents = array()) {
//        $apiKey = $this->apiKey;
//        $url = self::CHATGPT_HOST . '/chat/completions';

        $messages = array(
            array('role' => 'system', 'content' => '你是一个灵镜新世界的小灵镜，专门解答各种问题'),
            array('role' => 'user', 'content' => $userMessage)
        );

//        // 添加一组预定义的问题和答案
//        $templateMessages = array(
//            array('role' => 'assistant', 'content' => '回答问题1的内容'),
//            array('role' => 'assistant', 'content' => '回答问题2的内容'),
//            // 添加更多的问题和答案
//        );

        $templateMessages = array();
        if (!empty($templateContents)) {
            foreach ($templateContents as $templateContent) {
                $templateMessages[] = array('role' => 'assistant', 'content' => $templateContent);
            }
        }

        $data = array(
            'model' => 'gpt-3.5-turbo',  // 或者使用其他模型
            'messages' => array_merge($messages, $templateMessages),
            'temperature' => 0.7
        );



        $response = $this->_call('/chat/completions', $data, 'POST');

//        if (curl_errno($ch)) {
//            echo 'Error:' . curl_error($ch);
//        }

//        curl_close($ch);

        return json_decode($response, true);
    }

    public function text2Speech($text, $voice = 'alloy') {
        $data = array(
            'model' => 'tts-1',  // 或者使用其他模型
            'input' => $text,
            'voice' => $voice
        );

        $steam = $this->_call('/audio/speech', $data, 'POST');

        $tempFile = '/tmp/' . md5($text) . '.mp3';

        $saveFile = Yii::$app->basePath . '/web' . $tempFile;

        file_put_contents($saveFile, $steam);

        return $tempFile;
    }

    private function _formatResponse($response) {
        $messages = array();
        foreach ($response['choices'] as $choice) {
            $messages[] = array(
                'role' => 'assistant',
                'content' => $choice['message']['content']
            );
        }

        return $messages;
    }

    private function _call($interface, $params = array(), $method = 'POST') {
        $url = self::CHATGPT_HOST . $interface;

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        );

        if ($method == 'POST') {
            $response = Curl::curlPost($url, $params, $headers, true);
        } else {
            $response = Curl::curlGet($url);
        }

//        return json_decode($response, true);
        return $response;
    }

//// 示例调用
//$userMessage = '提问问题1';
//$response = callOpenAIChatGPT($userMessage);
//
//// 输出ChatGPT生成的回答
//echo $response['choices'][0]['message']['content'];


}