<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


class Model
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

    public static function encodeDialog($dialog) {
        if (empty($dialog)) {
            return $dialog;
        }
        eval('$dialog = ' . $dialog);
        if (is_array($dialog)) {
            if (!empty($dialog['Dialog'])) {
                $tmpDialog = [];
                foreach ($dialog['Dialog'] as $dia) {
                    $tmpDialog[] = $dia;
                }
                $dialog['Dialog'] = $tmpDialog;
            }
            return json_encode($dialog);
        }
        return $dialog;
    }

    public static function decodeDialog($dialogJson) {
        return json_decode($dialogJson, true);
    }

    public static function formatDialog($dialog, $params = []) {
        $ret = $dialog;
        if (!empty($params['user_id'])) {
            $ret = str_replace('{$user_id}', $params['user_id'], $ret);
        }
        if (!empty($params['session_id'])) {
            $ret = str_replace('{$session_id}', $params['session_id'], $ret);
        }
        if (!empty($params['session_stage_id'])) {
            $ret = str_replace('{$session_stage_id}', $params['session_stage_id'], $ret);
        }

        $jsonTmp = json_decode($ret, true);

        if (!empty($jsonTmp['Dialog'])) {
            foreach ($jsonTmp['Dialog'] as &$dia) {
                if (!empty($dia['sentenceClipURL'])) {
                    $dia['sentenceClipURL'] = Attachment::completeUrl($dia['sentenceClipURL'], false);
                }
            }
        }
        $ret = json_encode($jsonTmp);

        return $ret;
    }

}