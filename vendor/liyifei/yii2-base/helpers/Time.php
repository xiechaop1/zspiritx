<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Email: 119422342@qq.com
 * Date: 18-2-26
 * Time: 下午9:37
 */

namespace liyifei\base\helpers;


class Time
{
    /**
     * @desc 当日起始时间戳
     * @return false|int
     */
    public static function timestampStartToday()
    {
        return mktime(0, 0, 0);
    }

    /**
     * @desc 当日结束时间戳
     * @return false|int
     */
    public static function timestampEndToday()
    {
        return mktime(23, 59, 59);
    }

    /**
     * @desc 判断是否是今天
     * @param $timestamp
     * @return bool
     */
    public static function isToday($timestamp)
    {
        $end = static::timestampEndToday();

        return ($end > $timestamp) && (($end - $timestamp) < 86400);
    }

    /**
     * @desc 判断是否是昨天
     * @param $timestamp
     * @return bool
     */
    public static function isYesterday($timestamp)
    {
        $start = static::timestampStartToday();
        return ($timestamp < $start) && (($start - $timestamp) < 86400);
    }

    /**
     * @desc 友好得返回两个时间戳距离
     * @param $timestamp1
     * @param $timestamp2
     * @param $short
     * @return string
     */
    public static function friendlyDiff($timestamp1, $timestamp2, $short = false)
    {
        $datetime1 = new \DateTime(date('Y-m-d H:i:s', $timestamp1));
        $datetime2 = new \DateTime(date('Y-m-d H:i:s', $timestamp2));

        $interval = $datetime1->diff($datetime2);
        $time['y'] = (int)$interval->format('%Y');
        $time['m'] = (int)$interval->format('%m');
        $time['d'] = (int)$interval->format('%d');
        $time['h'] = (int)$interval->format('%H');
        $time['i'] = (int)$interval->format('%i');
        $time['s'] = (int)$interval->format('%s');
        $distance = '';

        if ($time['y']) {
            if ($short) {
                return $time['y'] . '年';
            }
            $distance .= $time['y'] . '年';
        }

        if ($time['m']) {
            if ($short) {
                return $time['m'] . '月';
            }
            $distance .= $time['m'] . '月';
        }

        if ($time['d']) {
            if ($short) {
                return $time['d'] . '天';
            }
            $distance .= $time['d'] . '天';
        }

        if ($time['h']) {
            if ($short) {
                return $time['h'] . '小时';
            }
            $distance .= $time['h'] . '小时';
        }

        if ($time['i']) {
            if ($short) {
                return $time['i'] . '分';
            }
            $distance .= $time['i'] . '分';
        }

        if ($time['s']) {
            if ($short) {
                return $time['s'] . '秒';
            }
            $distance .= $time['s'] . '秒';
        }

        return $distance;
    }

    /**
     * @desc 友好时间显示
     * @param $sTime
     * @param string $type
     * @return false|string
     */
    public static function friendly($sTime, $type = 'normal')
    {
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime = time();
        $dTime = $cTime - $sTime;
        $dDay = intval(date("Ymd", $cTime)) - intval(date("Ymd", $sTime));
        $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
        //normal：n秒前，n分钟前，n小时前，日期
        if ($type == 'full') {
            return date("Y-m-d , H:i:s", $sTime);
        } else {
            if ($dTime < 60 && $dTime > 0) {
                return $dTime . "秒前";
            } elseif ($dTime < 3600 && $dTime > 0) {
                return intval($dTime / 60) . "分钟前";
            } elseif ($dTime >= 3600 && $dDay == 0) {
                return intval($dTime / 3600) . "小时前";
            } elseif ($dDay == 1) {
                return date("昨天 H:i", $sTime);
            } elseif ($dDay == 2) {
                return date("前天 H:i", $sTime);
            } else {
                return date("Y-m-d", $sTime);
            }
        }
    }

    /**
     * @desc 获取星座
     * @param $month
     * @param $day
     * @return mixed
     */
    public static function getConstellation($month, $day)
    {
        $signs = array(
            array('20' => '宝瓶座'), array('19' => '双鱼座'),
            array('21' => '白羊座'), array('20' => '金牛座'),
            array('21' => '双子座'), array('22' => '巨蟹座'),
            array('23' => '狮子座'), array('23' => '处女座'),
            array('23' => '天秤座'), array('24' => '天蝎座'),
            array('22' => '射手座'), array('22' => '摩羯座')
        );
        $key = (int)$month - 1;
        list($startSign, $signName) = each($signs[$key]);
        if ($day < $startSign) {
            $key = $month - 2 < 0 ? $month = 11 : $month -= 2;
            list($startSign, $signName) = each($signs[$key]);
        }
        return $signName;
    }
}