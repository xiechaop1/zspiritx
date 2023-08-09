<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2018/4/19
 * Time: 下午3:27
 */

namespace liyifei\aliyunsms;


use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use yii\base\Component;

class Sms extends Component
{

    public $accessKeyId;

    public $accessKeySecret;

    public $signName;

    public $templateId;

    /**
     * @var DefaultAcsClient $_client
     */
    private $_client;

    private $_error;

    public function init()
    {
        parent::init();

        Config::load();

        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        //初始化acsClient,暂不支持region化l
        $profile = DefaultProfile::getProfile($region, $this->accessKeyId, $this->accessKeySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 初始化AcsClient用于发起请求
        $this->_client = new DefaultAcsClient($profile);
    }

    /**
     * @desc 发送验证码
     * @param $mobile
     * @param $params
     * @return mixed|\SimpleXMLElement
     */
    public function sendSms($mobile, $params = null)
    {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($mobile);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($this->signName);

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($this->templateId);

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        if ($params) {
            $request->setTemplateParam(json_encode($params, JSON_UNESCAPED_UNICODE));
        }

        // 发起访问请求
        $acsResponse = $this->_client->getAcsResponse($request);
        if ($acsResponse->Code !== "OK") {
            throw new \Exception($acsResponse->Message);
        }

        return [
            'requestId' => $acsResponse->RequestId,
            'BizId' => $acsResponse->BizId
        ];
    }

    // TODO
    public function batchSendSms()
    {

    }

}