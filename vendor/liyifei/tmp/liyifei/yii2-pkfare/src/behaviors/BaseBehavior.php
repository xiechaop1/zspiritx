<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 19-4-13
 * Time: 下午8:50
 */

namespace ota\pkfare\behaviors;


use yii\base\Component;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

class BaseBehavior extends Component
{
    public $partnerId;

    public $partnerKey;

    public $url;

    public $needDecode;

    public $debug;

    public $lastErrorCode;

    public function init()
    {
        if ($this->debug) {
            $this->url = $this->urlDebug;
        } else {
            $this->url = $this->urlProd;
        }
        parent::init();
    }

    public function send($get = [], $post = [])
    {
        $this->lastErrorCode = null;

        $client = new Client();
        $request = $client->createRequest();

        if ($get) {
            $this->url .= '?param=' . $this->_buildParam($get);
        }
        $request->setUrl($this->url);
        if ($post) {
            $request->setMethod('POST');
            $request->setData($post);
        }

        $response = $request->send();
        if ($response->isOk) {
            if ($this->needDecode) {
                $data = json_decode(zlib_decode($response->content), true);
            } else {
                $data = json_decode($response->content, true);
            }

            if ($data['errorCode'] !== '0') {
                $this->lastErrorCode = $data['errorCode'];
                throw new Exception($data['errorMsg']);
            }

            return $data['data'];
        } else {
            $this->lastErrorCode = 'HTTP_REQUEST_ERROR';

            throw new Exception("Pkfare request fail, status: {$response->statusCode}, message: {$response->content}");
        }
    }

    private function _buildParam($get)
    {
        $data = ArrayHelper::merge([
            'authentication' => [
                'partnerId' => $this->partnerId,
                'sign' => $this->_sign()
            ],

        ], $get);

        return base64_encode(json_encode($data));
    }

    private function _sign()
    {
        return md5($this->partnerId . $this->partnerKey);
    }
}