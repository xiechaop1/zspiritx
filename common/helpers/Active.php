<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


class Active
{
    /*
     * InFormat: Key1:Value1,Key2:Value2
     * OunputFormat: {"Key1":"Value1","Key2":"Value2"}
     */
    public static function encodeActive($active) {
        if (empty($active)) {
            return $active;
        }
        if (Common::isJson($active)) {
            return $active;
        }

        $activeArr = explode(',', $active);
        $activeTmp = [];
        foreach ($activeArr as $act) {
            $actArr = explode(':', $act);
            $activeTmp[$actArr[0]] = $actArr[1];
        }

        return json_encode($activeTmp);
    }

    /*
     * InFormat: {"Key1":"Value1","Key2":"Value2"}
     * OunputFormat: Key1:Value1,Key2:Value2
     */
    public static function decodeActiveToShow($active) {
        if (empty($active)) {
            return $active;
        }

        if (Common::isJson($active)) {
            $activeArr = json_decode($active, true);

//            $activeTmp = [];
            if (is_array($activeArr)) {
                foreach ($activeArr as $key => $act) {
                    $activeTmp[] = $key . ':' . $act;
                }
                $active = implode(',', $activeTmp);
            } else {
                $active = $activeArr;
            }
        }

        return $active;
    }

    public static function decodeActive($active) {
        if (Common::isJson($active)) {
            $activeArr = json_decode($active, true);
            return $activeArr;
        }

        return $active;
    }


}