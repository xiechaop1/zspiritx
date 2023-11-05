<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


class Common
{
    const PI = 3.1415926535898;
    const EARTH_RADIUS=6378.137;

    public static function getRealIP()
    {
        $ip = '';
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_FROM', 'REMOTE_ADDR') as $v) {
            if (isset($_SERVER[$v])) {
                if (! preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $_SERVER[$v])) {
                    continue;
                }
                $ip = $_SERVER[$v];
                break;
            }
        }

        return $ip;
    }

    /**
     * @param int $len
     * @param int $type ( 0 - 混合; 1 - 小写; 2 - 大写; 3 - 数字 )
     * @return string
     */
    public static function makeStr($len = 8, $initType = 0) {

        $lowerStart = ord('a');
        $lowerEnd   = ord('z');

        $upperStart = ord('A');
        $upperEnd   = ord('Z');

        $numStart   = 0;
        $numEnd     = 9;

        $str = '';
        for ($i=0; $i<$len; $i++) {

            $type = ($initType == 0) ? rand(1,3) : $initType;
            switch ($type) {
                case 1:
                    $str .= chr(rand($lowerStart, $lowerEnd));
                    break;
                case 2:
                    $str .= chr(rand($upperStart, $upperEnd));
                    break;
                case 3:
                    $str .= rand($numStart, $numEnd);
                    break;
            }

        }
        return $str;

    }
    public static function createWechatSign($url = null) {
        if (empty($url)) {
            $url = \Yii::$app->request->getUrl();
        }
        if (substr($url, 0, 1) == '/') {
            $hostInfo = \Yii::$app->request->hostInfo;
            $url = $hostInfo . $url;
        }
        $sign = \Yii::$app->hewaApi->getWechatSign([
            'url' => $url,
        ]);

        $ret = !empty($sign['code']) && $sign['code'] == 200 ? $sign['data'] : '';

        if ( is_string($ret) && strpos($ret, 'invalid signature') !== false) {
            return [];
        } else {
            return $ret;
        }
    }

    public static function showList($array, $val, $default = '')
    {

        return isset($array[$val])
            ? $array[$val]
            : $default;
    }


    /**
     * 给定一个ip 一个网段 判断该ip是否属于该网段
     * @param $ip
     * @param $networkRange
     * @return bool 属于返回true 不属于返回false
     */
    public static function judge($ip, $networkRange)
    {
        $ip = (double) (sprintf("%u", ip2long($ip)));
        $s = explode('/', $networkRange);
        $network_start = (double) (sprintf("%u", ip2long($s[0])));
        $network_len = pow(2, 32 - $s[1]);
        $network_end = $network_start + $network_len - 1;

        if ($ip >= $network_start && $ip <= $network_end) {
            return true;
        }
        return false;
    }

    public static function isChinaIp() {
        $ip = self::getRealIP();
        $ipRange = file( dirname(__FILE__) . '/../../china_ip.txt');

        foreach ($ipRange as $chinaIp) {
            $chinaIp = str_replace("\n", '', $chinaIp);
            if (self::judge($ip, $chinaIp)) {
                return true;
            }
        }
        return false;
    }

    public static function isJson($str) {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @param int $lenType 1 - 米; 1000 - 千米
     * @param int $decimal
     * @return float
     */
    public static function computeDistanceWithLatLng($lat1, $lng1, $lat2, $lng2, $lenType = 1, $decimal = 2) {
        $radLat1 = $lat1 * self::PI / 180.0;
        $radLat2 = $lat2 * self::PI / 180.0;
        $a = $radLat1 - $radLat2;
        $b = $lng1 * self::PI / 180.0 - $lng2 * self::PI / 180.0;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) +
                cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * self::EARTH_RADIUS;
        $s = round($s * 1000);

        if ($lenType > 1) {
            $s /= $lenType;
        }

        return round($s, $decimal);

    }


}