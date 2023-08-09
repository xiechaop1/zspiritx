<?php

namespace liyifei\DirectMail;

use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use liyifei\Dm\Request\V20151123 as Dm;

class Mail
{
    protected $region;
    protected $appKey;
    protected $appSecret;
    protected $accountName;
    protected $accountAlias;

    public function __construct($region, $appKey, $appSecret, $accountName, $accountAlias)
    {
        $this->region = $region;
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->accountName = $accountName;
        $this->accountAlias = $accountAlias;

        Config::load();
    }


    protected function createClient()
    {
        $iClientProfile = DefaultProfile::getProfile($this->region, $this->appKey, $this->appSecret);

        return new DefaultAcsClient($iClientProfile);
    }


    public function send($sendToAddress, $subject, $body)
    {
        $request = new Dm\SingleSendMailRequest();
        $request->setAccountName($this->accountName);
        $request->setFromAlias($this->accountAlias);
        $request->setAddressType(1);
        $request->setReplyToAddress('true');
        $sendToAddress = is_array($sendToAddress) ? implode(",", $sendToAddress) : $sendToAddress;
        $request->setToAddress($sendToAddress);
        $request->setSubject($subject);
        $request->setHtmlBody($body);

        try {
            $this->createClient()->getAcsResponse($request);
            return true;
        } catch (\Exception  $e) {
            throw $e;
        }
    }

}