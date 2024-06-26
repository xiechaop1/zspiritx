<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\Cookies;
use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\Session;
use common\models\SessionModels;
use common\models\SessionStages;
use common\models\StoryModels;
use common\models\StoryModelsLink;
use common\models\StoryStages;
use common\models\UserModelsUsed;
use common\services\Curl;
use common\models\User;
use common\helpers\Cookie;
use yii\base\Component;
use yii;

class Models extends Component
{

    const TIMEOUT_MAX = 60 * 60;

    const KEEP_ALIVE_TIMEOUT = 10;

    public static $prop2Name = [
        'strength' => '力量',
        'agility' => '敏捷',
        'intelligence' => '智力',
        'hp' => '生命值',
        'max_hp' => '最大生命值',
        'mp' => '魔法值',
        'max_mp' => '最大魔法值',
        'exp' => '经验值',
        'max_exp' => '最大经验值',
        'attack' => '攻击力',
        'defense' => '防御力',
    ];

    public function getUnderTakeModelsFromCookie(){

//        $stageCookieJson = Cookie::getCookie(Cookies::UPDATE_STAGE_TIME);
        $models = Cookie::getCookie(Cookies::UNDERTAKE_MODEL);
        Yii::info('Undertake models: ' . json_encode($models));

        return $models;

    }

    public function setUnderTakeStage($stageCookie){
        Cookie::unsetCookie(Cookies::UPDATE_STAGE_TIME);
        usleep(200);
        Cookie::setCookie(Cookies::UPDATE_STAGE_TIME, $stageCookie, self::TIMEOUT_MAX);
        Yii::info('Undertake: Update User Stage' . json_encode($stageCookie, true));
    }

    public function getUnderTakeStage() {
        $ret = $retJson = Cookie::getCookie(Cookies::UPDATE_STAGE_TIME);
//        $ret = json_decode($retJson);
        return $ret;
    }

    public function putUnderTakeModelsToCookie($sessionId, $storyStageId) {
        $sessModels = SessionModels::find()
            ->joinWith('storymodel')
            ->where([
                'session_id' => (int)$sessionId,
                'o_story_model.story_stage_id'  => $storyStageId,
            ])
            ->andFilterWhere([
                'o_story_model.is_undertake' => StoryModels::IS_UNDERTAKE_YES
            ])
            ->all();

        $underTake = [];
        if (!empty($sessModels)) {
            foreach ($sessModels as $sessModel) {
                if (!empty($sessModel->storymodel)) {
                    $sessStoryModel = $sessModel->storymodel;

                    $underTake[] = [
                        'story_model_id' => $sessStoryModel->id,
                        'story_model_detail_id' => $sessStoryModel->story_model_detail_id,
                        'model_inst_u_id' => $sessStoryModel->model_inst_u_id,
                        'lat' => $sessStoryModel->lat,
                        'lng' => $sessStoryModel->lng,
                        'misrange' => $sessStoryModel->misrange,
                        'trigger_misrange' => $sessStoryModel->trigger_misrange,
                        'undertake_trigger_timeout' => $sessStoryModel->undertake_trigger_timeout,
                        'undertake_alive_timeout' => $sessStoryModel->undertake_alive_timeout,
//                            'is_ready' => false,
                    ];
                }
            }
            Cookie::setCookie(Cookies::UNDERTAKE_MODEL, $underTake, self::TIMEOUT_MAX);
            Yii::info('Undertake: Update User model: ' . json_encode($underTake, true));
        }
    }

    public function updateUnderTakeReady($underTake, $underTakeStage, $lat, $lng) {
        $updateCt = 0;
        if (!empty($underTake)) {
            foreach ($underTake as $key => $item) {
                $misRange = $item['misrange'];
                $triggerMisRange = $item['trigger_misrange'];
                $modelInstUId = $item['model_inst_u_id'];

                if (!empty($item['lat']) && !empty($item['lng'])) {
                    $distance = \common\helpers\Common::computeDistanceWithLatLng($lat, $lng, $item['lat'], $item['lng']);
                    if ($distance <= $triggerMisRange) {
                        if (empty($item['is_ready']) || $item['is_ready'] != true) {
                            $underTake[$key]['is_ready'] = true;
                            $updateCt++;
                        }
                        if (empty($underTakeStage['ts']) || $underTakeStage['ts'] == 0) {
                            $underTakeStage['ts'] = time();
                        }
                    }
                }   else {
                    if (empty($item['is_ready']) || $item['is_ready'] != true) {
                        $underTake[$key]['is_ready'] = true;
                        $updateCt++;
                    }
                    if (empty($underTakeStage['ts']) || $underTakeStage['ts'] == 0) {
                        $underTakeStage['ts'] = time();
                    }
                }
            }
//            $underTakeJson = json_encode($underTake, true);
            if ($updateCt > 0) {
                Cookie::setCookie(Cookies::UNDERTAKE_MODEL, $underTake, self::TIMEOUT_MAX);
                $this->setUnderTakeStage($underTakeStage);
                Yii::info('Undertake: Update Undertake ready: ' . json_encode($underTake, true) . ' ' . json_encode($underTakeStage));
            }
        }
    }

    public function setKeepAlive(){
        $timeout = self::KEEP_ALIVE_TIMEOUT;
        $userKeepAlive = Cookie::getCookie(Cookies::USER_KEEP_ALIVE);
        if (!empty($userKeepAlive)) {
            Cookie::setCookie(Cookies::USER_KEEP_ALIVE, $userKeepAlive + 1, $timeout);
        } else {
            Cookie::setCookie(Cookies::USER_KEEP_ALIVE, 1, $timeout);
        }
        return true;
    }

    public function getKeepAlive() {
        $userKeepAlive = Cookie::getCookie(Cookies::USER_KEEP_ALIVE);
        return $userKeepAlive;
    }

    public function setUndertakeAction($sessionId, $userId ) {
        $stageCookie = $this->getUnderTakeStage();
        Yii::info('Undertake stageCookie: ' . json_encode($stageCookie));

        $underTakeIds = [];
        if (!empty($stageCookie)) {
            $ts = $stageCookie['ts'];
            if (empty($ts)) {
                return [];
            }
            $cookieStoryStageId = $stageCookie['story_stage_id'];
            $cookieSessionStageId = $stageCookie['session_stage_id'];
            $cookieStoryId = $stageCookie['story_id'];
            $underTakeModels = $this->getUnderTakeModelsFromCookie();

            $timeInterval = time() - $ts;
            if (!empty($underTakeModels)) {
                $userKeepAlive = $this->getKeepAlive();
                foreach ($underTakeModels as $model) {
                    $modelInstUId = $model['model_inst_u_id'];
                    $storyModelId = $model['story_model_id'];
                    $undertakeTriggerTimeout = $model['undertake_trigger_timeout'];
                    $undertakeAliveTimeout = $model['undertake_alive_timeout'];
                    $isReady = !empty($model['is_ready']) ? $model['is_ready'] : false;
                    Yii::info('Undertake timeInterval ' . $timeInterval . ' keep-alive: ' . $userKeepAlive . ' isReady: ' . $isReady);
                    if ($isReady) {
                        if ($timeInterval > $undertakeTriggerTimeout
                            && $userKeepAlive > $undertakeAliveTimeout
                        ) {
                            $ret = Yii::$app->act->add($sessionId, $cookieSessionStageId, $cookieStoryId, $userId, $modelInstUId, Actions::ACTION_TYPE_MODEL_DISPLAY);
                            $underTakeIds[] = $ret->id;
                        }
                    }

                }
            }

//            if ((time() - $ts) > $execTime) {
//                $userKeepAlive = Cookie::getCookie(Cookies::USER_KEEP_ALIVE);
//                Yii::info('Undertake userKeepAlive: ' . $userKeepAlive);
//                if ($userKeepAlive > $keepAlive) {
////                    $sessModels = SessionModels::find()
////                        ->joinWith('storymodel')
////                        ->where([
////                            'session_id' => (int)$sessionId,
////                            'story_stage_id'  => $stageCookie['story_stage_id'],
////                        ])
////                        ->andFilterWhere([
////                            'o_story_model.is_undertake' => StoryModels::IS_UNDERTAKE_YES
////                        ])
////                        ->all();
//                    $sessModels = Cookie::getCookie(Cookies::UNDERTAKE_MODEL);
//
//                    if (!empty($sessModels)) {
////                        $sessModels = json_decode($sessModelJson, true);
//                        $underTakeIds = [];
//                        foreach ($sessModels as $sessModel) {
//                            if (!empty($sessModel['is_ready']) && $sessModel['is_ready'] == true) {
//                                $ret = Yii::$app->act->add($sessionId, $cookieSessionStageId, $cookieStoryId, $userId, $sessModel['model_inst_u_id'], Actions::ACTION_TYPE_MODEL_DISPLAY);
//                                $underTakeIds[] = $ret->id;
//                                $isUndertake = true;
//                            }
//                        }
//                    }
//                }
//            }
        }
        return $underTakeIds;
    }

    public function readUndertakeActionAndUnsetCookie($underTakeIds) {
        if (!empty($underTakeIds)) {
            foreach ($underTakeIds as $actId) {
                Yii::$app->act->readOne($actId);
            }
            Cookie::unsetCookie(Cookies::UPDATE_STAGE_TIME);
            Cookie::unsetCookie(Cookies::UNDERTAKE_MODEL);
        }
    }

    public function removeUndertakeModelFromCookie($storyModelDetailId, $storyModelId) {
        $underTake = $this->getUnderTakeModelsFromCookie();
        if (!empty($underTake)) {
            foreach ($underTake as $key => $item) {
                if ( !empty($item['story_model_detail_id']) ) {
                    if ($item['story_model_detail_id'] == $storyModelDetailId) {
                        unset($underTake[$key]);
                    }
                } else {
                    if ($item['story_model_id'] == $storyModelId) {
                        unset($underTake[$key]);
                    }
                }
            }
            Cookie::setCookie(Cookies::UNDERTAKE_MODEL, $underTake, self::TIMEOUT_MAX);
            Yii::info('Undertake: Remove model from cookie: ' . json_encode($underTake, true));
        }
    }

    /**
     * 检查storyModelsLink（模型关联列表）是否已经被使用（userMuodelsUsedData）
     * 且检查storyModelsLink是否包含storyModel，并本次被使用
     * 如果没有包含，则返回3
     * 如果包含了，但是还有storyModelsLink里没有被使用的，则返回4
     * 如果包含了，且storyModelsLink里全都被使用了，则返回2
     * @param $storyModel
     * @param $storyModelsLink
     * @param $userModelsUsedData
     * @param $userId
     * @param $storyId
     * @param $sessionId
     * @return array|mixed
     */
    public function checkUserModelUsedByModels($storyModel, $storyModelsLink, $userModelsUsedData, $userId, $storyId, $sessionId) {
//        $targetStoryModelDetailId = $storyModelsLink->story_model_detail_id2;
//        $targetStoryModelId = $storyModelsLink->story_model_id2;
//        $userModelsUsedData = $this->getUserModelUsedByTarget($targetStoryModelDetailId, $targetStoryModelId, $userId, $storyId, $sessionId);

        $userModelUseds = [];
        if (!empty($userModelsUsedData)) {
            foreach ($userModelsUsedData as $userModelUsedData) {
                $userModelGroupName = !empty($userModelUsedData->group_name) ? $userModelUsedData->group_name : '';
                $userModelUsedsList[$userModelGroupName][] = [
                    'story_model_detail_id' => $userModelUsedData->story_model_detail_id,
                    'story_model_id'        => $userModelUsedData->story_model_id,
                ];
            }
        }

        $matchStoryModels = [];
        $noFoundRet = '';
        $noFoundType = 0;
        $partlyFoundRet = '';
        $partlyFoundType = 0;
//        $ret = 0;           // 尚有缺少物品
        if (!empty($storyModelsLink)) {
            $useStoryModel = 0;
            foreach ($storyModelsLink as $storyModelLink) {
                $storyModelGroupName = !empty($storyModelLink->group_name) ? $storyModelLink->group_name : '';
                if ($storyModelLink->story_model_id == '-1') {
                    // 如果完全没找到
                    $noFoundRet = $groupNoFoundRet[$storyModelGroupName] = $storyModelLink->eff_exec;
                    $noFoundType = $groupNoFoundType[$storyModelGroupName] = $storyModelLink->eff_type;
                    continue;
                } else if ($storyModelLink->story_model_id == '-2') {
                    // 如果是部分完成
                    $partlyFoundRet = $groupPartlyFoundRet[$storyModelGroupName] = $storyModelLink->eff_exec;
                    $partlyFoundType = $groupPartlyFoundType[$storyModelGroupName] = $storyModelLink->eff_type;
                    continue;
                } else {
//                if (!empty($storyModelLink->group_name)) {
                    $tmpStoryModel = [
                        'story_model_detail_id' => $storyModelLink->story_model_detail_id,
                        'story_model_id'        => $storyModelLink->story_model_id,
                    ];
//                    if (empty($matchStoryModels[$storyModelGroupName])) {
////                        $tmpStoryModel['is_used'] = 1;  // 默认已使用
//                        $matchStoryModels[$storyModelGroupName] = [
//                            'group_name' => $storyModelGroupName,
//                            'code' => 1,
//                            'eff_type' => 0,
//                            'eff_exec' => '',
//                        ];
//                    }

                    if (!empty($userModelUsedsList[$storyModelGroupName])) {
                        // 检查使用过的里是否包含
                        foreach ($userModelUsedsList[$storyModelGroupName] as $userModelUsed) {
                            if (
                                (!empty($storyModelLink->story_model_detail_id) && $userModelUsed['story_model_detail_id'] == $storyModelLink->story_model_detail_id)
                                ||
                                (!empty($storyModelLink->story_model_id) && $userModelUsed['story_model_id'] == $storyModelLink->story_model_id)
                            ) {
                                if (empty($tmpStoryModel['is_used'])) {
                                    $tmpStoryModel['is_used'] = 1;  // 之前就已经使用了
                                    $tmpStoryModel['group_name'] = $storyModelGroupName;
                                    $tmpStoryModel['eff_type'] = 0;
                                    $tmpStoryModel['eff_exec'] = '';
                                }
//                                if (empty($matchStoryModels[$storyModelGroupName]['code'])) {
//                                    $matchStoryModels[$storyModelGroupName] = [
//                                        'code' => 1,
//                                        'group_name' => $storyModelGroupName,
//                                        'eff_type' => 0,
//                                        'eff_exec' => '',
//                                    ];
//                                }
                                break;
                            }
                        }
                    }
                    // 检查本次提交的是否相同
                    if (empty($tmpStoryModel['is_used'])) {
                        if (
                            (!empty($storyModelLink->story_model_detail_id) && $storyModel->story_model_detail_id == $storyModelLink->story_model_detail_id)
                        ||
                            (!empty($storyModelLink->story_model_id) && $storyModel->id == $storyModelLink->story_model_id)
                        ) {
                            if ($useStoryModel == 0) {
                                $tmpStoryModel['is_used'] = 2; // 本次提交的match上，使用了
                                $tmpStoryModel['group_name'] = $storyModelGroupName;
                                $tmpStoryModel['eff_type'] = $storyModelLink->eff_type;
                                $tmpStoryModel['eff_exec'] = $storyModelLink->eff_exec;
                                $useStoryModel = 1;
                            } else {
                                $tmpStoryModel['is_used'] = 3; // 还有欠缺
                                $tmpStoryModel['group_name'] = $storyModelGroupName;
                                $tmpStoryModel['eff_type'] = 0;
                                $tmpStoryModel['eff_exec'] = '';
                            }

//                            if ($matchStoryModels[$storyModelGroupName]['code'] == 1 && $useStoryModel == 0) {
//                                $matchStoryModels[$storyModelGroupName] = [
//                                    'code' => 2,
//                                    'group_name' => $storyModelGroupName,
//                                    'eff_type' => $storyModelLink->eff_type,
//                                    'eff_exec' => $storyModelLink->eff_exec,
//                                ];
//                                $useStoryModel = 1;
//                            }
//                            break;
                        } else {
                            $tmpStoryModel['is_used'] = 3; // 还有欠缺
                            $tmpStoryModel['group_name'] = $storyModelGroupName;
                            $tmpStoryModel['eff_type'] = 0;
                            $tmpStoryModel['eff_exec'] = '';
//                            $matchStoryModels[$storyModelGroupName] = [
//                                'code' => 3,
//                                'group_name' => $storyModelGroupName,
//                                'eff_type' => 0,
//                                'eff_exec' => '',
//                            ];
                        }
                    }

                    $matchStoryModels[$storyModelGroupName][] = $tmpStoryModel;


//                    if ($tmpStoryModel['is_used'] == 2) {
//                        if ($matchStoryModels[$storyModelGroupName]['ret'] == 1) {
//                            $matchStoryModels[$storyModelGroupName] = [
//                                'ret' => 2,
//                                'eff_type' => $storyModelLink->eff_type,
//                                'eff_exec' => $storyModelLink->eff_exec,
//                            ];        // 本次提交的match上，使用了
//                        }
//                    } elseif (empty($tmpStoryModel['is_used'])) {
//                        $matchStoryModels[$storyModelGroupName] = [
//                            'ret' => 0,
//                            'eff_type' => 0,
//                            'eff_exec' => '',
//                        ];        // 还有欠缺
//                    }

//                    $matchStoryModels[$storyModelLink->group_name][] = $tmpStoryModel;
                }
            }
        }

        $ret = [];
        if (!empty($matchStoryModels)) {
            foreach ($matchStoryModels as $groupName => $matchRets) {
                $maxIsUsed[$groupName] = 0;
                foreach ($matchRets as $matchRet) {
                    if ($matchRet['is_used'] > $maxIsUsed[$groupName]) {
                        $maxIsUsed[$groupName] = $matchRet['is_used'];
                    }
                    if ($matchRet['is_used'] == 2) {
                        $match2[$groupName] = $matchRet;
                    }
                }
//                if ( empty($useStoryModel) || $useStoryModel != 1 ) {
//                    // 新模型没用上
//                    $ret = [
//                        'code' => 0,
//                        'eff_type' => !empty($groupNoFoundType[$groupName]) ? $groupNoFoundType[$groupName] : $noFoundType,
//                        'eff_exec' => !empty($groupNoFoundRet[$groupName]) ? $groupNoFoundRet[$groupName] : $noFoundRet,
//                    ];
//                    break;
//                } else if (!empty($matchRet['code']) && $matchRet['code'] == 2) {
//                    $ret = $matchRet;
//                    break;
//                } else if (!empty($matchRet['code']) && $matchRet['code'] == 3) {
//                    // 只有部分匹配上（或者全未匹配上）
//                    $matchRet['eff_type'] = !empty($groupPartlyFoundType[$groupName]) ? $groupPartlyFoundType[$groupName] : $partlyFoundType;
//                    $matchRet['eff_exec'] = !empty($groupPartlyFoundRet[$groupName]) ? $groupPartlyFoundRet[$groupName] : $partlyFoundRet;
//                    $ret = $matchRet;
//                    break;
//                } else {
//                    $ret = $matchRet;
//                }
            }

            foreach ($matchStoryModels as $groupName => $matchRet) {
                if (!empty($maxIsUsed[$groupName]) && $maxIsUsed[$groupName] == 2 && !empty($match2[$groupName])) {
                    $ret = $match2[$groupName];
                    $ret['code'] = $ret['is_used'];
                    $ret['group_name'] = $groupName;
                    break;
                } else if (!empty($maxIsUsed[$groupName]) && $maxIsUsed[$groupName] == 3) {
                    // 只有部分匹配上（或者全未匹配上）
                    if ( !empty($match2[$groupName])) {
                        $matchRet['code'] = 4;
                        $matchRet['eff_type'] = !empty($groupPartlyFoundType[$groupName]) ? $groupPartlyFoundType[$groupName] : $partlyFoundType;
                        $matchRet['eff_exec'] = !empty($groupPartlyFoundRet[$groupName]) ? $groupPartlyFoundRet[$groupName] : $partlyFoundRet;
                        $matchRet['group_name'] = $groupName;
                    } else {
                        $matchRet['code'] = 3;
                        $matchRet['eff_type'] = !empty($groupNoFoundType[$groupName]) ? $groupNoFoundType[$groupName] : $noFoundType;
                        $matchRet['eff_exec'] = !empty($groupNoFoundRet[$groupName]) ? $groupNoFoundRet[$groupName] : $noFoundRet;
                        $matchRet['group_name'] = $groupName;
                    }
                    $ret = $matchRet;
                    break;
                } else {
                    $ret = [
                        'code' => 0,
                        'eff_type' => 0,
                        'eff_exec' => '',
                    ];
                }
            }
        }

        return $ret;


    }

    public function checkUserModelUsedByStoryModel($storyModel, $targetStoryModel, $userId, $storyId, $sessionId, $userModelId = 0) {

        $matchUserModelUsed = UserModelsUsed::find()
            ->where([
                'user_id' => $userId,
                'story_id' => $storyId,
                'session_id' => $sessionId,
//                'group_name' => $storyModel->group_name,
                'use_status' => UserModelsUsed::USE_STATUS_WAITING
            ]);
        if (!empty($storyModel->use_group_name)) {
            $matchUserModelUsed->andFilterWhere(['group_name' => $storyModel->use_group_name]);
        }
        if (!empty($targetStoryModel->story_model_detail_id)) {
            $matchUserModelUsed->andFilterWhere(['story_model_detail_id2' => $targetStoryModel->story_model_detail_id]);
        } else {
            $matchUserModelUsed->andFilterWhere(['story_model_id2' => $targetStoryModel->id]);
        }
        $matchUserModelUsed = $matchUserModelUsed->orderBy([
            'story_model_id' => SORT_ASC
        ])
        ->all();

        if (!empty($matchUserModelUsed)) {
            $noFoundRet = $partlyFoundRet = '';
            $noFoundType = $partlyFoundType = 0;

            $modelCt = sizeof($matchUserModelUsed);
            foreach ($matchUserModelUsed as $userModelUsed) {
                if ($userModelUsed->story_model_id == '-1') {
                    // 如果完全没找到
                    $noFoundRet = $groupNoFoundRet[$userModelUsed->group_name] = $userModelUsed->eff_exec;
                    $noFoundType = $groupNoFoundType[$userModelUsed->group_name] = $userModelUsed->eff_type;
                    $noFoundModel = $userModelUsed;
                    $modelCt--;
                    continue;
                } else if ($userModelUsed->story_model_id == '-2') {
                    // 如果是部分完成
                    $partlyFoundRet = $groupPartlyFoundRet[$userModelUsed->group_name] = $userModelUsed->eff_exec;
                    $partlyFoundType = $groupPartlyFoundType[$userModelUsed->group_name] = $userModelUsed->eff_type;
                    $partlyFoundModel = $userModelUsed;
                    $modelCt--;
                    continue;
                } else {
                    if (
                        (!empty($userModelUsed->story_model_detail_id) && $storyModel->story_model_detail_id == $userModelUsed->story_model_detail_id)
                        ||
                        (!empty($userModelUsed->story_model_id) && $storyModel->id == $userModelUsed->story_model_id)
                    ) {
                        if ($modelCt > 1) {
                            // 部分匹配上
                            $matchRet = [
                                'code' => 4,
                                'eff_type' => $partlyFoundType,
                                'eff_exec' => $partlyFoundRet,
                                'group_name' => $userModelUsed->group_name,
                                'min_ct' => isset($userModelUsed->storyModelLink->min_ct) ? $userModelUsed->storyModelLink->min_ct : 1,
                            ];
                        } else {
                            // 完全匹配上
                            $matchRet = [
                                'code' => 2,
                                'eff_type' => $userModelUsed->eff_type,
                                'eff_exec' => $userModelUsed->eff_exec,
                                'group_name' => $userModelUsed->group_name,
                                'min_ct' => isset($userModelUsed->storyModelLink->min_ct) ? $userModelUsed->storyModelLink->min_ct : 1,
                            ];

                            if (!empty($noFoundModel)) {
                                $noFoundModel->use_status = UserModelsUsed::USE_STATUS_COMPLETED;
                                $noFoundModel->save();
                            }

                            if (!empty($partlyFoundModel)) {
                                $partlyFoundModel->use_status = UserModelsUsed::USE_STATUS_COMPLETED;
                                $partlyFoundModel->save();
                            }
                        }
                        $userModelUsed->user_model_id = $userModelId;
                        $userModelUsed->use_status = UserModelsUsed::USE_STATUS_COMPLETED;
                        $userModelUsed->save();

                        break;
                    }
                }
            }
                if (empty($matchRet)) {
                    // 未匹配上
                    $matchRet = [
                        'code' => 3,
                        'eff_type' => $noFoundType,
                        'eff_exec' => $noFoundRet,
                        'group_name' => '',
                        'min_ct' => 0,
                    ];
                }

            return $matchRet;
        } else {
            throw new \yii\base\Exception('您的使用没有任何效果', ErrorCode::USER_MODEL_NO_EFFECT);
        }

    }

    public function addPreUserModelUsedByGroup($groupName, $targetStoryModel, $userId, $storyId, $sessionId, $sessionStageId = 0, $useStatus = UserModelsUsed::USE_STATUS_WAITING) {
        $storyModelLinks = StoryModelsLink::find();
        if (!empty($groupName)) {
            $storyModelLinks->where([
                'group_name' => $groupName,
            ]);
        }
        if (!empty($targetStoryModel->story_model_detail_id)) {
            $storyModelLinks->andFilterWhere([
                'story_model_detail_id2' => $targetStoryModel->story_model_detail_id,
            ]);
        } else {
            $storyModelLinks->andFilterWhere([
                'story_model_id2' => $targetStoryModel->id,
            ]);
        }
        $storyModelLinks = $storyModelLinks->all();

        try {
            $transaction = Yii::$app->db->beginTransaction();
            if (!empty($storyModelLinks)) {
                $currentUserModelUsed = UserModelsUsed::find()
                    ->where([
                        'user_id' => $userId,
                        'story_id' => $storyId,
                        'session_id' => $sessionId,
//                        'session_stage_id' => $sessionStageId,
//                        'group_name' => $groupName,
                        'use_status' =>
                            [
                            UserModelsUsed::USE_STATUS_WAITING,
                            UserModelsUsed::USE_STATUS_COMPLETED_PARTLY,
                        ]
                    ]);
                if (!empty($sessionStageId)) {
                    $currentUserModelUsed->andFilterWhere(['session_stage_id' => $sessionStageId]);
                }
                if (!empty($groupName)) {
                    $currentUserModelUsed->andFilterWhere(['group_name' => $groupName]);
                }
                if (!empty($targetStoryModel->story_model_detail_id)) {
                    $currentUserModelUsed->andFilterWhere([
                        'story_model_detail_id2' => $targetStoryModel->story_model_detail_id,
                    ]);
                } else {
                    $currentUserModelUsed->andFilterWhere([
                        'story_model_id2' => $targetStoryModel->id,
                    ]);
                }
                $currentUserModelUsed = $currentUserModelUsed->all();

                $currentUMUCt = 0;
                if (!empty($currentUserModelUsed)) {
                    foreach ($currentUserModelUsed as $userModelUsed) {
                        if (!in_array($userModelUsed->story_model_id, [-1,-2])) {
                            $currentUMUCt++;
                        }
                    }
                }

                if ($currentUMUCt == 0) {
                    if (!empty($currentUserModelUsed)) {
                        foreach ($currentUserModelUsed as $userModelUsed) {
                            $userModelUsed->use_status = UserModelsUsed::USE_STATUS_COMPLETED;
                            $userModelUsed->save();
                        }
                    }
                    foreach ($storyModelLinks as $storyModelLink) {
                        $userModelUsed = new UserModelsUsed();
                        $userModelUsed->story_model_link_id = $storyModelLink->id;
                        $userModelUsed->user_id = $userId;
                        $userModelUsed->story_id = $storyId;
                        $userModelUsed->session_id = $sessionId;
                        $userModelUsed->session_stage_id = $sessionStageId;
                        $userModelUsed->group_name = $storyModelLink->group_name;
                        $userModelUsed->use_status = $useStatus;
                        $userModelUsed->story_model_id = $storyModelLink->story_model_id;
                        $userModelUsed->story_model_detail_id = $storyModelLink->story_model_detail_id;
                        $userModelUsed->story_model_id2 = $storyModelLink->story_model_id2;
                        $userModelUsed->story_model_detail_id2 = $storyModelLink->story_model_detail_id2;
                        $userModelUsed->eff_exec = $storyModelLink->eff_exec;
                        $userModelUsed->eff_type = $storyModelLink->eff_type;
                        $userModelUsed->save();
                    }
                }

            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

    }

    public function cancelUserModelUsedByStageId($sessionStageId, $sessionId, $userId, $storyId ) {
        $userModelUsed = UserModelsUsed::find()
            ->where([
                'user_id' => $userId,
                'story_id' => $storyId,
                'session_id' => $sessionId,
                'session_stage_id' => $sessionStageId,
                'use_status' => [
                    UserModelsUsed::USE_STATUS_WAITING,
                    UserModelsUsed::USE_STATUS_COMPLETED_PARTLY,
                ]
            ])
            ->all();

        if (!empty($userModelUsed)) {
            foreach ($userModelUsed as $tmp) {
                $tmp->use_status = UserModelsUsed::USE_STATUS_CANCEL;
                $tmp->save();
            }
        }

        return true;
    }

    public function addUserModelUsedByStoryModel($storyModelLinkId, $storyModel, $targetStoryModel, $userId, $storyId, $sessionId, $useStatus = UserModelsUsed::USE_STATUS_COMPLETED_PARTLY,  $groupName = '', $effExec = '', $effType = 0) {
        $userModelUsed = new UserModelsUsed();
        $userModelUsed->story_model_link_id = $storyModelLinkId;
        $userModelUsed->user_id = $userId;
        $userModelUsed->story_id = $storyId;
        $userModelUsed->session_id = $sessionId;
        $userModelUsed->story_model_detail_id = $storyModel->story_model_detail_id;
        $userModelUsed->story_model_id = $storyModel->id;
        $userModelUsed->story_model_detail_id2 = $targetStoryModel->story_model_detail_id;
        $userModelUsed->story_model_id2 = $targetStoryModel->id;
        $userModelUsed->group_name = $groupName;
        $userModelUsed->eff_exec = $effExec;
        $userModelUsed->eff_type = $effType;
        $userModelUsed->use_status = $useStatus;
        $userModelUsed->save();
    }

    public function updateUserModelUsedByTargetStoryModel($targetStoryModel, $userId, $storyId, $sessionId, $useStatus = UserModelsUsed::USE_STATUS_COMPLETED) {
        $userModelUsed = $this->getUserModelUsedByTarget($targetStoryModel->story_model_detail_id, $targetStoryModel->id, $userId, $storyId, $sessionId);

        if (!empty($userModelUsed)) {
            foreach ($userModelUsed as $tmp) {
                $tmp->use_status = $useStatus;
                $tmp->save();
            }
        }

        return true;
    }

    public function getUserModelUsedByTarget($targetStoryModelDetailId, $targetStoryModelId, $userId, $storyId, $sessionId) {
        $userModelUsed = UserModelsUsed::find()
            ->where([
                'user_id'       => $userId,
                'story_id'      => $storyId,
                'session_id'    => $sessionId,
                'use_status'    => UserModelsUsed::USE_STATUS_COMPLETED_PARTLY,
            ]);

        if (!empty($targetStoryModelDetailId)) {
            $userModelUsed->andFilterWhere([
                'story_model_detail_id2' => $targetStoryModelDetailId,
            ]);
        } else {
            $userModelUsed->andFilterWhere([
                'story_model_id2' => $targetStoryModelId,
            ]);
        }
//var_dump($userModelUsed->createCommand()->getRawSql());exit;
        $userModelUsed = $userModelUsed->all();

        return $userModelUsed;
    }

    public function computeStoryModelPropWithFormula($useStoryModels, $targetStoryModel, $targetUserModel = [], $useStoryModelIds = []) {
        $ret = [];
        $combineList = [];
        if (!empty($targetStoryModel->story_model_prop)) {
            $tarStoryModelProp = json_decode($targetStoryModel->story_model_prop, true);
            if (!empty($tarStoryModelProp['formula'])) {
                $formula = $tarStoryModelProp['formula'];
            }
        }
        if (empty($formula)) {
            return $ret;
        }

        if (!empty($targetUserModel)) {
            $userModelProp = !empty($targetUserModel->user_model_prop) ? json_decode($targetUserModel->user_model_prop, true) : [];
            $combineList = !empty($userModelProp['combine_story_model_list']) ? $userModelProp['combine_story_model_list'] : [];

            if (!empty($combineList)) {
                foreach ($useStoryModelIds as $useStoryModelId) {
                    if (!in_array($useStoryModelId, $combineList)) {
                        $combineList[] = $useStoryModelId;
                    }
                }

                $useStoryModels = StoryModels::find()
                    ->where([
                        'id' => $combineList,
                    ])
                    ->all();


            } else {
                $combineList = $useStoryModelIds;
            }
        } else {
            $combineList = $useStoryModelIds;
        }

//        preg_match_all('/\{\$(.+?)\}/', $formula, $matches);
        if ( !empty($useStoryModels)
//            && !empty($matches)
        ) {
            foreach ($useStoryModels as $usm) {

                $storyModelProp = !empty($usm->story_model_prop) ? json_decode($usm->story_model_prop, true) : [];
                $storyModelProp = !empty($storyModelProp['prop']) ? $storyModelProp['prop'] : [];
//                $storyModelProp = !empty($usm->story_model_prop) ? json_decode($usm->story_model_prop, true) : [];
//                $storyModelProp = !empty($storyModelProp['prop']) ? $storyModelProp['prop'] : [];
                $storyModel = $usm;
//                var_dump($storyModelProp);
//                foreach ($matches[1] as $match) {
//                    $formula = str_replace('{$' . $match . '}', $storyModelProp[$match], $formula);
//                }
//                echo $formula;
//                exit;
                eval( $formula . ';');
            }
        }

        return [
            'prop' => $ret,
            'combine_story_model_list' => $combineList,
        ];
    }

    // 检查storyModelIds是否包含在linkStoryModelIds里（选中模型是否全部在待选模型中）
    // 并且检查linkStoryModelTagIds是否包含在storyModelIds里（必要模型是否都在）
    // @return bool/array
    // 如果返回true，则表示全部符合
    // 如果返回false，则表示选中模型不全是待选模型，有偏差
    // 如果返回Array(ids)，则表示必要模型不在选中模型中，并返回了区别模型的ID
    public function checkStoryModelWithLinkStoryModel($storyModelIds, $linkStoryModelIds, $linkStoryModelTagIds) {
        $tagCheck = \common\helpers\Common::arrayContains($linkStoryModelTagIds, $storyModelIds);
        if ( $tagCheck !== true) {
            return $tagCheck;
        }

        $linkCheck = \common\helpers\Common::arrayContains($storyModelIds, $linkStoryModelIds);
        if ( $linkCheck !== true) {
            return false;
        } else {
            return true;
        }
    }

    public function computeAddStoryModelLinkPropWithFormula($linkExecArr, $targetUserModel = []) {
        $ret = [];
        $combineList = [];

        if (empty($targetUserModel)) {
            return $ret;
        }

        $oldProp = [];
        if (!empty($targetUserModel)) {
            $tarUserModelProp = !empty($targetUserModel->user_model_prop) ? json_decode($targetUserModel->user_model_prop, true) : [];
            $combineList = !empty($userModelProp['combine_story_model_list']) ? $userModelProp['combine_story_model_list'] : [];

            if (empty($tarUserModelProp['prop'])) {
                $targetStoryModel = !empty($targetUserModel->storyModel) ? $targetUserModel->storyModel : [];
                if (empty($targetStoryModel)) {
                    return $ret;
                }

                $tarStoryModelProp = !empty($targetStoryModel->story_model_prop) ? json_decode($targetStoryModel->story_model_prop, true) : [];

                if (!empty($tarStoryModelProp['prop'])) {
                    $ret = $tarStoryModelProp['prop'];
                    $storyModelFormula = $tarStoryModelProp['formula'];
                }
            } else {
                $ret = $tarUserModelProp['prop'];
            }
        }
        $oldProps = $ret;

        $up = [];

        if (!empty($linkExecArr)) {
            foreach ($linkExecArr as $linkExec) {
                if (\common\helpers\Common::isJson($linkExec)) {
                    $linkExec = json_decode($linkExec, true);
                }
                $userModelProp = !empty($linkExec['user_model_prop']) ? $linkExec['user_model_prop'] : [];
                $formula = !empty($linkExec['formula']) ? $linkExec['formula'] : '';

                if (empty($formula)
                    && !empty($storyModelFormula)
                ) {
                    $formula = $storyModelFormula;
                }

                if (empty($formula)) {
                    continue;
                }

                eval( $formula . ';');

                if (!empty($ret)) {
                    foreach ($ret as $k => $v) {
                        $oldProp = !empty($oldProps[$k]) ? $oldProps[$k] : 0;
                        if (($v - $oldProp) > 0) {
                            $up[$k] = [
                                'title' => !empty(self::$prop2Name[$k]) ? self::$prop2Name[$k] : ' - ',
                                'value' => $v - $oldProp,
                            ];
                        }
                    }
                }
            }
        }

        return [
            'data' => ['prop' => $ret],
            'up' => $up,
        ];

    }

    public function computeStoryModelLinkPropWithFormula($linkExecArr, $targetStoryModel, $targetUserModel = [], $useStoryModelIds = [], $needRecordList = true) {
        $ret = [];
        $combineList = [];
//        if (!empty($targetStoryModel->story_model_prop)) {
//            $tarStoryModelProp = json_decode($targetStoryModel->story_model_prop, true);
//            if (!empty($tarStoryModelProp['formula'])) {
//                $formula = $tarStoryModelProp['formula'];
//            }
//        }
//        if (empty($formula)) {
//            return $ret;
//        }

        if (!empty($targetUserModel)) {
            $userModelProp = !empty($targetUserModel->user_model_prop) ? json_decode($targetUserModel->user_model_prop, true) : [];
            $combineList = !empty($userModelProp['combine_story_model_list']) ? $userModelProp['combine_story_model_list'] : [];

            if (!empty($combineList)) {
                foreach ($useStoryModelIds as $useStoryModelId) {
                    if (!in_array($useStoryModelId, $combineList)) {
                        $combineList[] = $useStoryModelId;
                    }
                }

//                $useStoryModels = StoryModels::find()
//                    ->where([
//                        'id' => $combineList,
//                    ])
//                    ->all();


            } else {
                $combineList = $useStoryModelIds;
            }
        } else {
            $combineList = $useStoryModelIds;
        }

//        preg_match_all('/\{\$(.+?)\}/', $formula, $matches);
        if ( !empty($combineList)
//            && !empty($matches)
        ) {
            $formula = '';
            foreach ($combineList as $storyModelId) {

                if (!empty($linkExecArr[$storyModelId]['prop'])) {
                    $storyModelProp = $linkExecArr[$storyModelId]['prop'];
                } else {
                    $storyModelProp = [];
                }

                $formula = !empty($linkExecArr[$storyModelId]['formula']) ? $linkExecArr[$storyModelId]['formula'] : $formula;
                if (empty($formula)) {
                    return [
                        'prop' => '',
                        'combine_story_model_list' => [],
                    ];
                }

//                $storyModelProp = !empty($usm->story_model_prop) ? json_decode($usm->story_model_prop, true) : [];
//                $storyModelProp = !empty($storyModelProp['prop']) ? $storyModelProp['prop'] : [];
//                $storyModelProp = !empty($usm->story_model_prop) ? json_decode($usm->story_model_prop, true) : [];
//                $storyModelProp = !empty($storyModelProp['prop']) ? $storyModelProp['prop'] : [];
//                $storyModel = $usm;
//                var_dump($storyModelProp);
//                foreach ($matches[1] as $match) {
//                    $formula = str_replace('{$' . $match . '}', $storyModelProp[$match], $formula);
//                }
//                echo $formula;
//                exit;
                eval( $formula . ';');
            }
        }

        if (!$needRecordList) {
            $combineList = [];
        }

        return [
            'prop' => $ret,
            'combine_story_model_list' => $combineList,
        ];
    }

    public function computeUserModelPropWithStoryModel($storyModel) {
        $ret = [];
        $formula = '';
        if (!empty($storyModel->story_model_prop)) {
            $storyModelProp = json_decode($storyModel->story_model_prop, true);
            if (!empty($storyModelProp['init_formula'])) {
                $formula = $storyModelProp['init_formula'];
            }
        }
        if (empty($formula)) {
            return $ret;
        }

        eval( $formula . ';');

//        $ret['prop'] = $ret;

        return $ret;
    }

    public function checkLevel($userModelProp) {
//        $userModelProp = !empty($userModel->user_model_prop) ? json_decode($userModel->user_model_prop, true) : '';

        $formula = [
//            'exp' => '$newExp = 115 ^ ($level - 1) + 4;',
            'level' => '$newProp["level"] = $level + 1;',
            'intelligence' => '$newProp["intelligence"] = intval($intelligence + 20 * pow(1.02, ($level - 1)) + 5);',
            'strength' => '$newProp["strength"] = intval($strength + 20 * pow(1.02, ($level - 1)) + 5);',
            'agility' => '$newProp["agility"] = intval($agility + 20 * pow(1.02, ($level - 1)) + 5);',
            'attack' => '$newProp["attack"] = intval($attack + 20 * pow(1.02, ($level - 1)) + 5);',
            'defense' => '$newProp["defense"] = intval($defense + 15 * pow(1.02, ($level - 1)) + 5);',
            'att_speed' => '$newProp["att_speed"] = number_format(60 / ($newProp["agility"] / 30), 2);',
            'max_hp' => '$newProp["max_hp"] = intval($newProp["strength"] * pow(1.02, ($level - 1)))+200;',
            'max_mp' => '$newProp["max_mp"] = intval($newProp["intelligence"] * pow(1.02, ($level - 1)))+150;',
            'max_exp' => '$newProp["max_exp"] = intval(200 * pow(1.22, $level) + 4);',
            'hp' => '$newProp["hp"] = $newProp["max_hp"];',
            'mp' => '$newProp["mp"] = $newProp["max_mp"];',
//            'att_speed' => '$newProp["att_speed"] = intval($newProp["agility"] * pow(1.02, ($level - 1)));',
        ];

        $newProp = [];
        $up = [];
        $levelup = false;
        if (!empty($userModelProp)) {
            $newProp = $prop = !empty($userModelProp['prop']) ? $userModelProp['prop'] : [];
            if (!empty($prop)) {
                $level = !empty($prop['level']) ? $prop['level'] : 1;
                $exp = !empty($prop['exp']) ? $prop['exp'] : 0;
                $maxExp = !empty($prop['max_exp']) ? $prop['max_exp'] : 0;

//                if ($level == 1) {
//                    $maxExp = 4;
//                } else {
//                    $maxExp = 200 * pow(1.22, $level) + 4;
//                }
//                $maxExp = intval($maxExp);

                $newProp['exp'] = $exp;
//var_dump($exp);var_dump($maxExp);exit;
                if ($exp >= $maxExp) {
                    foreach ($formula as $col => $for) {
                        $$col = !empty($prop[$col]) ? $prop[$col] : 0;
                        eval($for . ';');
//                        $newProp[$col] = $col;
                    }
                    $levelup = true;
                }
            }
            if (!empty($newProp)) {
                foreach ($newProp as $k => $v) {
                    $userModelProp['prop'][$k] = $v;
                    $oldProp = !empty($prop[$k]) ? $prop[$k] : 0;
                    $up[$k] = [
                        'title' => !empty(self::$prop2Name[$k]) ? self::$prop2Name[$k] : ' - ',
                        'value' => intval($v - $oldProp),
                    ];
//                    $upProp[$k] = intval($newProp[$k] - $prop[$k]);
                }
            }
        }

        return [
            'isUp' => $levelup,
            'up' => $up,
            'data' => $userModelProp,
        ];

//        $userModel->user_model_prop = json_encode($newProp, true);
//
//        return $userModel;
    }

}