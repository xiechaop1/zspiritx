<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2018/2/23
 * Time: 上午11:24
 */

namespace liyifei\base\helpers;


class Common
{

    /**
     * @desc 生成随机字符串
     * @param int $length
     * @param bool $is_int
     * @return string
     */
    public static function nonceStr($length = 32, $is_int = false)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        if ($is_int)
            $chars = "0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return strtoupper($str);
    }

    /**
     * @desc 友好的数字显示
     * @param $num
     * @return string
     */
    public static function friendlyNumber($num)
    {
        if ($num >= 10000) {
            $num = round($num / 10000 * 100) / 100 . 'W';
        } elseif ($num >= 1000) {
            $num = round($num / 1000 * 100) / 100 . 'K';
        } else {
            return $num;
        }
        return $num;
    }

}