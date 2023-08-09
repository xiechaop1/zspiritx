<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 19-4-13
 * Time: 下午8:52
 */

namespace ota\pkfare\libs;


use yii\httpclient\Client;

class Request
{
    public function send($url, $get = [], $post = [])
    {
        $client = new Client();
        $request = $client->createRequest();

        if ($get) {
            $url .= '?' . http_build_query($get);
        }

        $request->setUrl($url);
        if ($post) {
            $request->setMethod('POST');
            $request->setData($post);
        }
    }

    private function _sign()
    {

    }
}