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

    public function callOpenAIChatGPT($userMessage) {
        $apiKey = $this->apiKey;
        $url = self::CHATGPT_HOST . '/chat/completions';

        var_dump(Curl::curlPost($url, [], []));exit;

        $messages = array(
            array('role' => 'system', 'content' => '你是一个灵镜新世界的小灵镜，专门解答各种问题'),
            array('role' => 'user', 'content' => $userMessage)
        );

        // 添加一组预定义的问题和答案
        $templateMessages = array(
            array('role' => 'assistant', 'content' => '回答问题1的内容'),
            array('role' => 'assistant', 'content' => '回答问题2的内容'),
            // 添加更多的问题和答案
        );

        $data = array(
            'messages' => array_merge($messages, $templateMessages),
            'temperature' => 0.7
        );

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        );


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:8118");
//        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);
var_dump($response);exit;
        return json_decode($response, true);
    }

//// 示例调用
//$userMessage = '提问问题1';
//$response = callOpenAIChatGPT($userMessage);
//
//// 输出ChatGPT生成的回答
//echo $response['choices'][0]['message']['content'];
    public function login($code) {
        $uri = '/sns/jscode2session';

        $params = [
            'appid'     => $this->appId,
            'secret'    => $this->appSecret,
            'js_code'   => $code,
            'grant_type'=> 'authorization_code',
        ];

        $uri = $this->_createUri($uri, self::WECHAT_HOST, $params);

        try {
            $ret = $this->_getApi($uri);
//            var_dump($ret);exit;
            if (!empty($ret['errcode']) && $ret['errcode'] != 0) {
                throw new \Exception($ret['errmsg'], $ret['errcode']);
            }

            // 如果用户不存在，创建用户
            $openId = $ret['openid'];
            $user = User::findOne(['wx_openid' => $openId]);

            if (empty($user)) {

                // 获取手机号
                $mobile = $this->getMobile($code);

                $user = User::findOne(['mobile' => $mobile]);

                // 判断用户状态（是不是在白名单里，也就是状态是"被邀请"）
                if (empty($user)
                    || $user->user_status == User::USER_STATUS_FORBIDDEN
                ) {
                    throw new \Exception('用户不存在或已被禁用', -1001);
                } else {
                    $user->wx_openid = $openId;
                    $user->wx_unionid = $ret['unionid'];
                    $user->user_status = User::USER_STATUS_NORMAL;

                }

                // 创建新用户
//                $user = new User();
//                $user->open_id = $openId;
//                $user->union_id = $ret['unionid'];
//                $user->mobile = $mobile;
//                $user->save();
//                $user->id = Yii::$app->db->getLastInsertID();
            }
            $this->_token = $this->getToken();
            $user->wx_token = $this->_token;
            $user->save();

            return $user;

        } catch (\Exception $e) {
            throw new \Exception('微信登录失败：' . $e->getMessage());
        }

        return $ret;
    }

    public function getSession($code)
    {
        $uri = '/sns/jscode2session';

        $params = [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        ];

        $uri = $this->_createUri($uri, self::WECHAT_HOST, $params);

        try {
            $ret = $this->_getApi($uri);
        } catch (\Exception $e) {
            throw new \Exception('获取Session失败：' . $e->getMessage(), $e->getCode());
        }

        return $ret;
    }
    public function getMobile($code){
        $uri = '/wxa/business/getuserphonenumber';

        $tokenRet = $this->getToken();
        $token = !empty($tokenRet['access_token']) ? $tokenRet['access_token'] : '';

        $params = [
            'access_token'  => $token,
        ];

        $uri = $this->_createUri($uri, self::WECHAT_HOST, $params);

        try {
            $params = [
                'code' => $code
            ];
            $ret = $this->_getPostApi($uri, $params);

            return !empty($ret['phone_info']['phoneNumber']) ? $ret['phone_info']['phoneNumber'] : '';

        } catch (\Exception $e) {
            throw new \Exception('获取微信手机号失败：' . $e->getMessage());
        }

        return $ret;
    }

    public function getToken() {
        if (!empty($this->_token)) {
            return $this->_token;
        }
        $uri = '/cgi-bin/token';

        $params = [
            'grant_type'    => 'client_credential',
            'appid'         => $this->appId,
            'secret'        => $this->appSecret,
        ];

        $uri = $this->_createUri($uri, self::WECHAT_HOST, $params);

        try {
            $ret = $this->_getApi($uri);

            if (!empty($ret['access_token'])) {
                $this->_token = $ret;
//                return $ret;
            } else {
                throw new \Exception('获取微信token失败：' . $ret['errmsg'], $ret['errcode']);
            }

        } catch (\Exception $e) {
            throw new \Exception('获取微信token失败：' . $e->getMessage());
        }

        return $ret;
    }

    public function decryptData($encryptedData, $iv, $sessionKey) {
        $decrypted = openssl_decrypt($encryptedData, 'aes-128-cbc', base64_decode($sessionKey), OPENSSL_RAW_DATA);
        return $decrypted;
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

    private function _getPostApi($uri, $postParams = []) {
        $ret = Curl::curlPost($uri, $postParams, [], true);
        $ret = json_decode($ret, true);
        if (!empty($ret['errcode']) && $ret['errcode'] != 0) {
            throw new \Exception($ret['errmsg'], $ret['errcode']);
        }
        return $ret;
    }

}