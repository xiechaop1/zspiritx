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

    public static function encodeDialog($dialog, $model = []) {
        if (empty($dialog)) {
            return $dialog;
        }
        eval('$dialog = ' . $dialog);
        if (is_array($dialog)) {
            if (!empty($dialog['template'])) {
                switch ($dialog['template']) {
                    case 'pickup':
                        $dialogName = !empty($dialog['name']) ? $dialog['name'] : $model->model_inst_u_id;
                        $dialogDesc = !empty($dialog['desc']) ? $dialog['desc'] : $model->story_model_name;
                        $modelName = !empty($dialog['model_name']) ? $dialog['model_name'] : $model->model_inst_u_id;
                        $storyModelId = !empty($dialog['story_model_id']) ? $dialog['story_model_id'] : '{$story_model_id}';
                        $dialog = array (
                            'Name' => $dialogDesc,
                            'Intro' => $dialogName . '-dialog-0',
                            'ActionOnPlaced' =>
                                array (
                                    'localID' => $dialogName . '-OnPlaced',
                                    'moveX' => 0,
                                    'moveY' => 0,
                                    'moveZ' => 0,
                                ),
                            'Dialog' =>
                                array (
                                    array (
                                        'localID' => $dialogName . '-dialog-0',
                                        'name' => $dialogDesc,
                                        'sentence' => '发现' . $dialogDesc,
                                        'quizID' => 0,
                                        'sentenceClip' => '',
                                        'url' => '',
                                        'userSelections' =>
                                            array (
                                            ),
                                        'nextID' =>
                                            array (
                                                $dialogName . '-dialog-1',
                                            ),
                                    ),
                                    array (
                                        'localID' => $dialogName . '-dialog-1',
                                        'name' => $dialogDesc,
                                        'sentence' => '',
                                        'quizID' => 0,
                                        'sentenceClip' => '',
                                        'url' => 'https://h5.zspiritx.com.cn/processh5/pickup?user_id={$user_id}&session_id={$session_id}&session_stage_id={$session_stage_id}&story_id={$story_id}&story_model_id=' . $storyModelId . '&lock_ct=1',
                                        'viewPort' => 30,
                                        'hideModels' =>
                                            array (
                                                $modelName,
                                            ),
                                        'userSelections' =>
                                            array (
                                            ),
                                        'nextID' =>
                                            array (
                                                $dialogName . '-dialog-End',
                                            ),
                                    ),
                                    array (
                                        'localID' => $dialogName . '-dialog-End',
                                        'name' => '',
                                        'sentence' => '',
                                        'quizID' => 0,
                                        'sentenceClip' => '',
                                        'userSelections' =>
                                            array (
                                            ),
                                        'nextID' =>
                                            array (
                                                $dialogName . '-dialog-0',
                                            ),
                                    ),
                                ),
                        );
                }
            }
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

    public static function formatResources($resources) {
        if (empty($resources)) {
            return $resources;
        }

        if (Common::isJson($resources)) {
            $resourcesArr = json_decode($resources, true);
        } else {
            $resourcesArr = $resources;
        }

        if (is_array($resourcesArr)) {
            $resources = [];
            foreach ($resourcesArr as $type => $allPlatforms) {

                foreach ($allPlatforms as $platform => $res) {
                    if (!empty($res) && is_array($res)) {
                        foreach ($res as &$row) {
                            $row = Attachment::completeUrl('/resourcepackage/' . $type . '/' . $row, false);
                        }
                    }
                    $ret[$type][$platform] = $res;
                }
            }
        } else {
            $ret = $resourcesArr;
        }

        return $ret;
    }

    public static function formatStoryModel($storyModel, $params = [])
    {
        $colList = [
            'model_inst_u_id' => '',
            'story_model_name' => '',
//            'dialog' => '',
        ];
        if (!empty($params)) {
            foreach ($colList as $col => $val) {
                if (!empty($storyModel->$col)) {
                    foreach ($params as $key => $val) {
                        $storyModel->$col = str_replace('{$' . $key . '}', $val, $storyModel->$col);
                    }
                }
            }
        }

        return $storyModel;

    }

    public static function formatDialog($storyModel, $params = []) {
        if (!empty($storyModel->dialog)) {
            $ret = $storyModel->dialog;
        } else {
            return '';
        }

        $ret = Common::formatUrlParams($ret, $params);

//        if (!empty($params['user_id'])) {
//            $ret = str_replace('{$user_id}', $params['user_id'], $ret);
//        }
//        if (!empty($params['session_id'])) {
//            $ret = str_replace('{$session_id}', $params['session_id'], $ret);
//        }
//        if (!empty($params['session_stage_id'])) {
//            $ret = str_replace('{$session_stage_id}', $params['session_stage_id'], $ret);
//        }
//        if (!empty($params['story_id'])) {
//            $ret = str_replace('{$story_id}', $params['story_id'], $ret);
//        }
//        if (!empty($params['model_id'])) {
//            $ret = str_replace('{$model_id}', $params['model_id'], $ret);
//        }
//        if (!empty($params['story_model_id'])) {
//            $ret = str_replace('{$story_model_id}', $params['story_model_id'], $ret);
//        }
//        if (!empty($params['story_model_detail_id'])) {
//            $ret = str_replace('{$story_model_detail_id}', $params['story_model_detail_id'], $ret);
//        }
//        if (!empty($params['model_inst_u_id'])) {
//            $ret = str_replace('{$model_inst_u_id}', $params['model_inst_u_id'], $ret);
//        }

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

                if (!empty($dia['bgmURL'])) {
                    $dia['bgmURL'] = Attachment::completeUrl($dia['bgmURL'], false);
                }

                if (!empty($dia['passiveModels'])) {
                    $tmpModels = [];
                    foreach ($dia['passiveModels'] as $idx => $passiveModelInstUid) {
                        if (strpos($passiveModelInstUid, '[GROUP]') !== false) {
                            $groupName = str_replace('[GROUP]', '', $passiveModelInstUid);
                            $groupStoryModels = StoryModels::find()->where(['model_group' => $groupName, 'story_id' => $storyModel->story_id])->all();
                            if (!empty($groupStoryModels)) {
                                foreach ($groupStoryModels as $groupStoryModel) {
                                    $tmpModels[] = $groupStoryModel->model_inst_u_id;
                                }
                            }
                            unset($dia['passiveModels'][$idx]);
                            $dia['passiveModels'] = array_merge($dia['passiveModels'], $tmpModels);
                        }
                    }
                }

                if (!empty($dia['activeModels'])) {
                    $tmpModels = [];
                    foreach ($dia['activeModels'] as $idx => $activeModelInstUid) {
                        if (strpos($activeModelInstUid, '[GROUP]') !== false) {
                            $groupName = str_replace('[GROUP]', '', $activeModelInstUid);
                            $groupStoryModels = StoryModels::find()->where(['model_group' => $groupName, 'story_id' => $storyModel->story_id])->all();
                            if (!empty($groupStoryModels)) {
                                foreach ($groupStoryModels as $groupStoryModel) {
                                    $tmpModels[] = $groupStoryModel->model_inst_u_id;
                                }
                            }
                            unset($dia['activeModels'][$idx]);
                            $dia['activeModels'] = array_merge($dia['activeModels'], $tmpModels);
                        }
                    }
                }
            }
        }
        $ret = json_encode($jsonTmp);

        return $ret;
    }

    public static function getUserModelProp($userModel, $att = 'user_model_prop') {
        $ret = [];
        if (!empty($userModel->$att)) {
//            var_dump($userModel->$att);
            $ret = json_decode($userModel->$att, true);
        }
//        var_dump($ret);exit;
        return $ret;
    }

    public static function getUserModelPropCol($userModel, $col, $att = 'user_model_prop') {
        $userModelProp = self::getUserModelProp($userModel, $att);

        if (isset($userModelProp['prop'][$col])) {
            return $userModelProp['prop'][$col];
        }
        return '';
    }

    public static function getUserModelPropColWithPropJson($userModelProp, $col) {
        if (isset($userModelProp['prop'][$col])) {
            return $userModelProp['prop'][$col];
        }
        return '';
    }

    public static function setUserModelPropColWithPropJson($userModelProp, $col, $val) {
        $userModelProp['prop'][$col] = $val;
        return $userModelProp;
    }

    public static function setUserModelPropCol($userModel, $col, $val, $att = 'user_model_prop') {
        $userModelProp = self::getUserModelProp($userModel, $att);
        $userModelProp['prop'][$col] = $val;
        $userModel->$att = json_encode($userModelProp);
        return $userModel;
    }

    public static function addUserModelPropCol($userModel, $col, $addition = 1, $att = 'user_model_prop') {
        $userModelProp = self::getUserModelProp($userModel, $att);
        $userModelProp['prop'][$col] += $addition;
        $userModel->$att = json_encode($userModelProp);
        return $userModel;
    }

    public static function addUserModelPropColWithPropJson($userModelProp, $col, $addition = 1) {
        $userModelProp['prop'][$col] += $addition;
        return $userModelProp;
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