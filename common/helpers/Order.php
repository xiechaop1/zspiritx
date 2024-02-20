<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


class Order
{
    /*
     * 生成订单号
     */
    public static function generateOutTradeNo($userId, $mobile, $storyId, $storyType, $payMethod) {

        $ret = substr($mobile, 0, -4) . self::genNum(2, $storyType) . self::genNum(6, $userId) . self::genNum(4, $storyId) . self::genNum(2, $payMethod) . time() . rand(1000,9999);

        return $ret;
    }

    public static function genNum($length = 6, $str) {

        $strLen = strlen($str);

        $ret = '';
        for ($i=0; $i<($length - $strLen); $i++) {
            $ret .= '0';
        }
        $ret .= $str;

        return $ret;
    }

}