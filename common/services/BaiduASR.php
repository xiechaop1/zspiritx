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

class BaiduASR extends Component
{

    // define('DEMO_CURL_VERBOSE', false); // 打印curl debug信息


    const AMAP_HOST = 'https://restapi.amap.com';

    const DEMO_CURL_VERBOSE = false;

    public $appKey;
    public $appSecret;

    const CUID = "123456PHP";
    # 采样率
    const RATE = 16000;  // 固定值

    # 普通版
    const ASR_URL = "http://vop.baidu.com/server_api";
    # 根据文档填写PID，选择语言及识别模型
    const DEV_PID = 1537; //  1537 表示识别普通话，使用输入法模型。
    const SCOPE = 'audio_voice_assistant_get'; // 有此scope表示有语音识别普通版能力，没有请在网页里开通语音识别能力

    #测试自训练平台需要打开以下信息， 自训练平台模型上线后，您会看见 第二步：“”获取专属模型参数pid:8001，modelid:1234”，按照这个信息获取 dev_pid=8001，lm_id=1234
    //const DEV_PID = 8001 ;
    //const LM_ID = 1234 ;

    # 极速版需要打开以下信息 打开注释的话请填写自己申请的appkey appSecret ，并在网页中开通极速版（开通后可能会收费）
//    const ASR_URL = "http://vop.baidu.com/pro_api";
//    const DEV_PID = 80001;
//    const SCOPE = 'brain_enhanced_asr';  // 有此scope表示有极速版能力，没有请在网页里开通极速版
    
    //const SCOPE = false; // 部分历史应用没有加入scope，设为false忽略检查


    public function getToken() {
        /** 公共模块获取token开始 */

        $auth_url = "http://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id=".$this->appKey."&client_secret=".$this->appSecret;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $auth_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 检查证书中是否设置域名,0不验证
        curl_setopt($ch, CURLOPT_VERBOSE, self::DEMO_CURL_VERBOSE);
        $res = curl_exec($ch);
        if(curl_errno($ch))
        {
            print curl_error($ch);
        }
        curl_close($ch);

//        echo "Token URL response is " . $res . "\n";
        $response = json_decode($res, true);

        if (!isset($response['access_token'])){
            throw new \Exception('ERROR TO OBTAIN TOKEN', 1);
//            echo "ERROR TO OBTAIN TOKEN\n";
//            exit(1);
        }
        if (!isset($response['scope'])){
            throw new \Exception('ERROR TO OBTAIN scopes', 2);
//            echo "ERROR TO OBTAIN scopes\n";
//            exit(2);
        }

        if (self::SCOPE && !in_array(self::SCOPE, explode(" ", $response['scope']))){
            throw new \Exception('CHECK SCOPE ERROR', 3);
            //echo "CHECK SCOPE ERROR\n";
            // 请至网页上应用内开通语音识别权限
            //exit(3);
        }

//        $token = $response['access_token'];
//        echo "token = $token ; expireInSeconds: ${response['expires_in']}\n\n";
        return $response;
    }


    public function asrByFile($audioFile, $format = 'wav') {

        $tokenRes = $this->getToken();
        $token = $tokenRes['access_token'];



        /** 拼接参数开始 **/
        $audio = file_get_contents($audioFile);
        $base_data = base64_encode($audio);
        $params = array(
            "dev_pid" => self::DEV_PID,
            //"lm_id" => $LM_ID,    //测试自训练平台开启此项
            "format" => $format,
            "rate" => self::RATE,
            "token" => $token,
            "cuid"=> self::CUID,
            "speech" => $base_data,
            "len" => strlen($audio),
            "channel" => 1,
        );

        $response = $this->_getPostApi(self::ASR_URL, $params);

//        $json_array = json_encode($params);
//        $headers[] = "Content-Length: ".strlen($json_array);
//        $headers[] = 'Content-Type: application/json; charset=utf-8';
//
//        /** 拼接参数结束 **/
//
//        /** asr 请求开始 **/
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, self::ASR_URL);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 识别时长不超过原始音频
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_array);
//        curl_setopt($ch, CURLOPT_VERBOSE, self::DEMO_CURL_VERBOSE);
//        $res = curl_exec($ch);
//        if(curl_errno($ch))
//        {
//            echo curl_error($ch);
//            exit (2);
//        }
//        curl_close($ch);
//        /** asr 请求结束 **/
//
//        $response = json_decode($res, true);
        file_put_contents("/tmp/asr.log", json_encode($response, JSON_UNESCAPED_UNICODE));

        if (isset($response['err_no']) && $response['err_no'] == 0){
            return $response;
//            echo "asr result is ". $response['result'][0] . "\n";
        }else{
            throw new \Exception('asr has error', $response['err_no']);
//            echo "asr has error\n";
        }


//
//        $token = $this->getToken();
//        $audio = base64_encode($audio);
//        $audio = urlencode($audio);
//        $url = self::ASR_URL . "?cuid=" . self::CUID . "&token=" . $token['access_token'] . "&dev_pid=" . self::DEV_PID;
//        $url .= "&rate=" . self::RATE . "&format=wav&channel=1&len=" . strlen($audio) . "&speech=" . $audio;
//        $ret = $this->_getApi($url);
//        return $ret;
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