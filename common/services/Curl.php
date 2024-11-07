<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/09/27
 * Time: 下午4:33
 */

namespace common\services;

class Curl
{
    /**

     * 通过CURL发送HTTP请求

     * @param string $url  //请求URL

     * @param array $postFields //请求参数

     * @return mixed

     */

    public static function curlPost($url,$postFields, $header = array(), $isJson = false, $opts = [], $isStream = false){

        if (!$isJson) {
            $postFields = http_build_query($postFields);
//            $postFields = json_encode($postFields);
        } else {
            $postFields = json_encode($postFields);

            $header[] = 'Content-Type: application/json; charset=utf-8';
            $header[] = 'Content-Length: ' . strlen($postFields);

        }


        $ch = curl_init ();

        curl_setopt ( $ch, CURLOPT_POST, 1 );

        curl_setopt ( $ch, CURLOPT_HEADER, 0 );

        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        curl_setopt ( $ch, CURLOPT_URL, $url );

        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );

        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );


        if (!empty($opts['CURLOPT_CONNECTTIMEOUT'])) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $opts['CURLOPT_CONNECTTIMEOUT']);
        }

        if (!empty($opts['CURLOPT_TIMEOUT'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $opts['CURLOPT_TIMEOUT']);
        }

//        $callback = 'streamCall';
        if ($isStream) {
            if (!empty($opts['callback'])) {
                $callback = $opts['callback'];
                $params = !empty($opts['callback_params']) ? $opts['callback_params'] : [];
            } else {
                $callback = ['\common\helpers\Stream', 'streamCallbackToText'];
                $params = !empty($opts['callback_params']) ? $opts['callback_params'] : [];
            }
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use ($callback, $params)
            {
                // 调用回调函数处理数据
                if (!empty($params)) {
                    $callback($data, $params);
                } else {
                    $callback($data);
                }
                return strlen($data); // 返回接收到的数据长度
            });
        }


//        if (!empty($opts)) {
//            foreach ($opts as $key => $value) {
//                curl_setopt($ch, $key, $value);
//            }
//        }

        $result = curl_exec ( $ch );

        if (curl_errno($ch)) {
//            echo 'Error:' . curl_error($ch);
            $result = curl_error($ch);
        }

        curl_close ( $ch );

        return $result;

    }

    public static function streamCall($data) {

        $dataJson = str_replace('data: ', '', $data);
        $dataArray = json_decode($dataJson, true);

        if (isset($dataArray['choices'][0]['delta']['content'])) {
            echo $dataArray['choices'][0]['delta']['content'] . str_repeat('        ', 128);
//            echo $dataArray['choices'][0]['delta']['content'] . ' 111';
        }
//        else {
//            print_r($dataArray);
//        }
        ob_flush();
        flush();
//        echo $dataArray['choices'][0]['delta']['content'] . ' ';
//        print $dataArray['choices'][0]['delta']['content'];
//        if (!empty($dataArray['choices'][0]['delta']['content'])) {
//            echo $dataArray['choices'][0]['delta']['content'];
//        }
//        ob_flush();
//        flush();

//        var_dump($data);
//        ob_flush();
//        flush();
    }

    public static function curlGet($url, $header = 0){

        $ch = curl_init ();

        curl_setopt ( $ch, CURLOPT_HEADER, $header );

        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

        curl_setopt ( $ch, CURLOPT_URL, $url );

        $result = curl_exec ( $ch );

        curl_close ( $ch );

        return $result;

    }
}
