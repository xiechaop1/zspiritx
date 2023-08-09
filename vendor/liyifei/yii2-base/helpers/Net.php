<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2018/3/3
 * Time: 下午1:09
 */

namespace liyifei\base\helpers;

use yii;

class Net
{
    public static function get($key, $defaultValue = '')
    {
        return Yii::$app->request->get($key, $defaultValue);
    }

    public static function post($key, $default = '')
    {
        return Yii::$app->request->post($key, $default);
    }

    /**
     * @desc 真实IP
     * @param bool $onlyremote
     * @return mixed
     */
    public static function realIp($onlyremote = false)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!$onlyremote) {
            if (isset($_SERVER['HTTP_CDN_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CDN_REAL_IP'])) {
                $ip = $_SERVER['HTTP_CDN_REAL_IP'];
            } elseif (isset($_SERVER['HTTP_CDN_SRC_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CDN_SRC_IP'])) {
                $ip = $_SERVER['HTTP_CDN_SRC_IP'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else if (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
                foreach ($matches[0] AS $xip) {
                    if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                        $ip = $xip;
                        break;
                    }
                }
            }
        }
        return $ip;
    }
}