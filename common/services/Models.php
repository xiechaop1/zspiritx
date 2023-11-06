<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\Cookies;
use common\models\Actions;
use common\models\Session;
use common\models\SessionModels;
use common\models\SessionStages;
use common\models\StoryModels;
use common\models\StoryStages;
use common\services\Curl;
use common\models\User;
use common\helpers\Cookie;
use yii\base\Component;
use yii;

class Models extends Component
{

    const TIMEOUT_MAX = 60 * 60;

    const KEEP_ALIVE_TIMEOUT = 10;

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

    public function removeUndertakeModelFromCookie($storyModelDetailId) {
        $underTake = $this->getUnderTakeModelsFromCookie();
        if (!empty($underTake)) {
            foreach ($underTake as $key => $item) {
                if ($item['story_model_detail_id'] == $storyModelDetailId) {
                    unset($underTake[$key]);
                }
            }
            Cookie::setCookie(Cookies::UNDERTAKE_MODEL, $underTake, self::TIMEOUT_MAX);
            Yii::info('Undertake: Remove model from cookie: ' . json_encode($underTake, true));
        }
    }

}