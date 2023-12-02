<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 3:24 PM
 */

namespace common\helpers;


use common\models\StoryModels;

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

    public static function formatDialog($storyModel, $params = []) {
        if (!empty($storyModel->dialog)) {
            $ret = $storyModel->dialog;
        } else {
            return '';
        }

        if (!empty($params['user_id'])) {
            $ret = str_replace('{$user_id}', $params['user_id'], $ret);
        }
        if (!empty($params['session_id'])) {
            $ret = str_replace('{$session_id}', $params['session_id'], $ret);
        }
        if (!empty($params['session_stage_id'])) {
            $ret = str_replace('{$session_stage_id}', $params['session_stage_id'], $ret);
        }
        if (!empty($params['story_id'])) {
            $ret = str_replace('{$story_id}', $params['story_id'], $ret);
        }
        if (!empty($params['story_model_id'])) {
            $ret = str_replace('{$story_model_id}', $params['story_model_id'], $ret);
        }
        if (!empty($params['model_inst_u_id'])) {
            $ret = str_replace('{$model_inst_u_id}', $params['model_inst_u_id'], $ret);
        }

        $jsonTmp = json_decode($ret, true);

        // 拼装互斥
        if (
            empty($jsonTmp['ActionOnPlaced']['hideModels'])
            && sizeof($storyModel->groupStoryModels) > 1
        ) {
            $dia = self::createActionOnPlaced($jsonTmp, $storyModel->model_group);
            foreach ($storyModel->groupStoryModels as $groupStoryModel) {
                if ($groupStoryModel->id != $storyModel->id) {
                    $jsonTmp['ActionOnPlaced']['hideModels'][] = $groupStoryModel->model_inst_u_id;
                }
            }
        }

        if (!empty($jsonTmp['Dialog'])) {
            foreach ($jsonTmp['Dialog'] as &$dia) {
                // 格式化URL
                if (!empty($dia['sentenceClipURL'])) {
                    $dia['sentenceClipURL'] = Attachment::completeUrl($dia['sentenceClipURL'], false);
                }

                if (!empty($dia['passiveModels'])) {
                    foreach ($dia['passiveModels'] as $idx => $passiveModelInstUid) {
                        if (strpos($passiveModelInstUid, '[GROUP]') !== false) {
                            $groupName = str_replace('[GROUP]', '', $passiveModelInstUid);
                            $groupStoryModels = StoryModels::find()->where(['model_group' => $groupName, 'story_id' => $storyModel->story_id])->all();
                            if (!empty($groupStoryModels)) {
                                foreach ($groupStoryModels as $groupStoryModel) {
                                    $dia['passiveModels'][] = $groupStoryModel->model_inst_u_id;
                                }
                            }
                            unset($dia['passiveModels'][$idx]);
                        }
                    }
                }

                if (!empty($dia['activeModels'])) {
                    foreach ($dia['activeModels'] as $idx => $activeModelInstUid) {
                        if (strpos($activeModelInstUid, '[GROUP]') !== false) {
                            $groupName = str_replace('[GROUP]', '', $activeModelInstUid);
                            $groupStoryModels = StoryModels::find()->where(['model_group' => $groupName, 'story_id' => $storyModel->story_id])->all();
                            if (!empty($groupStoryModels)) {
                                foreach ($groupStoryModels as $groupStoryModel) {
                                    $dia['activeModels'][] = $groupStoryModel->model_inst_u_id;
                                }
                            }
                            unset($dia['activeModels'][$idx]);
                        }
                    }
                }
            }
        }
        $ret = json_encode($jsonTmp);

        return $ret;
    }

    public static function createActionOnPlaced($dialog, $tag = '') {
        if (empty($dialog['ActionOnPlaced'])) {
            $dialog['ActionOnPlaced'] = [
                'localID'   => $tag . '-OnPlaced'
            ];
        }
        return $dialog;
    }

    public static function combineStoryModelWithDetail($storyModel) {
        $blackList = [
            'story_id', 'id',
        ];
        if (!empty($storyModel->detail)) {

            $detail = $storyModel->detail;
            foreach ($detail as $key => $val) {
                if (!empty($val)
                && isset($storyModel->$key)
                ) {
                    if (!in_array($key, $blackList)) {
                        $storyModel->$key = $val;
                    }
                }
            }

        }

        return $storyModel;
    }

}