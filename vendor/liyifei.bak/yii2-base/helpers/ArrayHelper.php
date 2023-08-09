<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2018/4/14
 * Time: 下午2:22
 */

namespace liyifei\base\helpers;


class ArrayHelper extends \yii\helpers\ArrayHelper
{

    /**
     * @desc 从数组中随机取出指定长度的数据
     * @param $array
     * @param $count
     * @return array
     */
    public static function random($array, $count)
    {
        $keys = array_rand($array, min($count, count($array)));

        if ($keys) {
            $rand = [];
            foreach ($keys as $key) {
                $rand[] = $array[$key];
            }
            return $rand;
        }

        return [];
    }

}