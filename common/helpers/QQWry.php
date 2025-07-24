<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;

/**
 * 纯真IP库查询类
 * 用法：$location = QQWry::query('8.8.8.8');
 */
class QQWry
{
    protected static $fp = null;
    protected static $ipBegin = 0;
    protected static $ipEnd = 0;

    public static function query($ip, $datFile = null)
    {
        if ($datFile === null) {
            $datFile = \Yii::getAlias('@common/data/qqwry.dat');
        }
        if (!file_exists($datFile)) {
            return false;
        }
        self::$fp = fopen($datFile, 'rb');
        if (!self::$fp) {
            return false;
        }
        self::$ipBegin = self::getLong4(0);
        self::$ipEnd = self::getLong4(4);
        $ip = gethostbyname($ip);
        $ip = self::ip2long($ip);
        $l = 0;
        $r = (self::$ipEnd - self::$ipBegin) / 7;
        $find = false;
        while ($l <= $r) {
            $m = floor(($l + $r) / 2);
            $seek = self::$ipBegin + $m * 7;
            fseek(self::$fp, $seek);
            $ip1 = self::getLong4();
            $offset = self::getLong3();
            fseek(self::$fp, $offset);
            $ip2 = self::getLong4();
            if ($ip >= $ip1 && $ip <= $ip2) {
                $find = true;
                break;
            }
            if ($ip < $ip1) {
                $r = $m - 1;
            } else {
                $l = $m + 1;
            }
        }
        if (!$find) {
            fclose(self::$fp);
            return false;
        }
        $location = self::getLocation();
        fclose(self::$fp);
        return $location;
    }

    protected static function getLong4($offset = -1)
    {
        if ($offset > -1) {
            fseek(self::$fp, $offset);
        }
        $result = unpack('Vlong', fread(self::$fp, 4));
        return $result['long'];
    }

    protected static function getLong3()
    {
        $result = unpack('Vlong', fread(self::$fp, 3) . chr(0));
        return $result['long'];
    }

    protected static function ip2long($ip)
    {
        $ip = explode('.', $ip);
        return $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
    }

    protected static function getString()
    {
        $str = '';
        while (1) {
            $c = fread(self::$fp, 1);
            if ($c == chr(0)) break;
            $str .= $c;
        }
        return iconv('GBK', 'UTF-8//IGNORE', $str);
    }

    protected static function getLocation()
    {
        $flag = ord(fread(self::$fp, 1));
        if ($flag == 1) {
            $offset = self::getLong3();
            fseek(self::$fp, $offset);
            $flag = ord(fread(self::$fp, 1));
        }
        if ($flag == 2) {
            $offset = self::getLong3();
            fseek(self::$fp, $offset);
            $country = self::getString();
            fseek(self::$fp, $offset + strlen($country) + 1);
            $area = self::getArea();
        } else {
            fseek(self::$fp, -1, SEEK_CUR);
            $country = self::getString();
            $area = self::getArea();
        }
        return $country . ' ' . $area;
    }

    protected static function getArea()
    {
        $flag = ord(fread(self::$fp, 1));
        if ($flag == 1 || $flag == 2) {
            $offset = self::getLong3();
            if ($offset == 0) {
                return '';
            } else {
                fseek(self::$fp, $offset);
                return self::getString();
            }
        } else {
            fseek(self::$fp, -1, SEEK_CUR);
            return self::getString();
        }
    }
}