<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\helpers\Model;
use common\models\SessionModels;
use common\models\StoryModels;
use common\models\UserModelLoc;
use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class UserModels extends Component
{

    public function setUserModelToLoc($storyId, $sessionId, $userLng, $userLat, $storyModelClass = 0, $radius = 1000, $limit = 30) {
        $userModelLocRets = $this->getUserModelLoc($userLng, $userLat, $radius, $limit);

        $result = [];
        $storyModelsResult = [];
        if (!empty($userModelLocRets)) {
            $userModelLocKv = !empty($userModelLocRets['userModelLocsKv']) ? $userModelLocRets['userModelLocsKv'] : [];
            $locations = !empty($userModelLocRets['locations']) ? $userModelLocRets['locations'] : [];
            $locationIds = !empty($userModelLocRets['locationIds']) ? $userModelLocRets['locationIds'] : [];
            $userModelLocs = !empty($userModelLocRets['userModelLocs']) ? $userModelLocRets['userModelLocs'] : [];

//            $storyModelsKv = !empty($userModelLocRets['storyModelsKv']) ? $userModelLocRets['storyModelsKv'] : [];
            if (!empty($locationIds)) {
                foreach ($locationIds as $locId) {
                    if (isset($userModelLocKv[$locId])) {
                        $result[$locId][] = [
                            'userModelLoc' => $userModelLocKv[$locId],
                            'location' => $locations[$locId],
                        ];
                        if (!empty($userModelLocKv[$locId])) {
                            foreach ($userModelLocKv[$locId] as $userModelLoc) {
                                $storyModelsResult[$userModelLoc->story_model_id][] = [
                                    'userModelLoc' => $userModelLoc,
                                    'location' => $locations[$locId],
                                ];
                            }
                        }
                        continue;
                    }

//                    $storyModelClass = !empty($storyModelClass) ? $storyModelClass
//                        : [StoryModels::STORY_MODEL_CLASS_PET, StoryModels::STORY_MODEL_CLASS_RIVAL];

                    if (empty($storyModelClass)) {
                        if (!empty($locations[$locId]['location_typecode'])) {
                            $locationTypeCode = $locations[$locId]['location_typecode'];
                            if (substr($locationTypeCode, 0, 4) == '1203'
                                || substr($locationTypeCode, 0, 3) == '140'
                            ) {
                                $storyModelClassFind = StoryModels::STORY_MODEL_CLASS_RIVAL;
                                $rate = rand(1, 100);
                                foreach (StoryModels::$storyModelClassRate as $tempStoryModelClass => $tempRate) {
                                    if ($rate <= $tempRate) {
                                        $storyModelClassFind = $tempStoryModelClass;
                                        break;
                                    }
                                }
                            } else {
                                $storyModelClassFind = StoryModels::STORY_MODEL_CLASS_RIVAL;
                            }
                        } else {
                            if (!empty($locations[$locId]['location_type'])) {
                                $locationTypes = explode(';', $locations[$locId]['location_type']);
                                if ($locationTypes[0] == '风景名胜' || $locationTypes[1] == '住宅区') {
                                    $storyModelClassFind = StoryModels::STORY_MODEL_CLASS_RIVAL;
                                    $rate = rand(1, 100);
                                    foreach (StoryModels::$storyModelClassRate as $tempStoryModelClass => $tempRate) {
                                        if ($rate <= $tempRate) {
                                            $storyModelClassFind = $tempStoryModelClass;
                                            break;
                                        }
                                    }
                                } else {
                                    $storyModelClassFind = StoryModels::STORY_MODEL_CLASS_RIVAL;
                                }
                            } else {
                                $storyModelClassFind = StoryModels::STORY_MODEL_CLASS_RIVAL;
                            }
                        }


                    } else {
                        $storyModelClassFind = $storyModelClass;
                    }

                    $storyModels = StoryModels::find()
                        ->where([
                            'story_model_class' => $storyModelClassFind
                        ])
                        ->orderBy('rand()')
                        ->limit($limit)
                        ->one();

                    if (!empty($storyModels)) {
                        $storyModel = $storyModels;
//                        foreach ($storyModels as $storyModel) {

                            $propArray = Model::getUserModelProp($storyModel, 'story_model_prop');
                            $formula = !empty($propArray['init_formula']) ? $propArray['init_formula'] : '';

                            if (!empty($formula)) {
                                eval($formula . ';');
                                $userModelProp = $ret;
                            } else {
                                $userModelProp = [];
                            }

                            if ($storyModel->story_model_class == StoryModels::STORY_MODEL_CLASS_PET) {
                                $activeClass = UserModelLoc::ACTIVE_CLASS_CATCH;
                            } elseif ($storyModel->story_model_class == StoryModels::STORY_MODEL_CLASS_RIVAL) {
                                $activeClass = UserModelLoc::ACTIVE_CLASS_BATTLE;
                            } else {
                                $activeClass = UserModelLoc::ACTIVE_CLASS_OTHER;
                            }

                            $amapPoiId = !empty($locations[$locId]['amap_poi_id']) ? $locations[$locId]['amap_poi_id'] : '';
                            $userModelLoc = $this->addUserModelLoc(0, 0,
                                $locId, $amapPoiId, $storyId, $sessionId,
                                $storyModel->id, $storyModel->story_stage_id, $activeClass, $userModelProp);
//                            $userModelLoc = new UserModelLoc();
//                            $userModelLoc->user_id = 0;
//                            $userModelLoc->location_id = $locId;
//                            $userModelLoc->story_model_id = $storyModel->id;
//                            $userModelLoc->story_id = $storyId;
//                            $userModelLoc->session_id = $sessionId;
//                            $userModelLoc->amap_poi_id = $locations[$locId]->amap_poi_id;
//                            $userModelLoc->user_model_prop = $userModelProp;
//                            $userModelLoc->user_model_loc_status = UserModelLoc::USER_MODEL_LOC_STATUS_LIVE;
//                            $userModelLoc->save();
//                            $userModelLoc->id = \Yii::$app->db->getLastInsertID();

                            $result[$locId][] = [
                                'userModelLoc' => [$userModelLoc],
                                'location' => $locations[$locId],
//                                'storyModels' => $storyModel,
                            ];
//                            $userModelLoc;
                            $storyModelsResult[$storyModel->id][] = [
                                'userModelLoc' => $userModelLoc,
                                'location' => $locations[$locId],
//                                'storyModels' => $storyModel,
                            ];
//                            $userModelLoc;
//                        }
                    }

                }
            }
        }
        return [
            'result' => $result,
            'storyModelsResult' => $storyModelsResult,
        ];

    }

    public function checkUniqueUserModelLocWithLngLat($locLng, $locLat, $uniqueList, $radius = 50, $userId = 0) {
        if (!empty($uniqueList)) {
            foreach ($uniqueList as $uniOne) {
                $uniLng = !empty($uniOne['lng']) ? $uniOne['lng'] : 0;
                $uniLat = !empty($uniOne['lat']) ? $uniOne['lat'] : 0;

                $dis = \common\helpers\Common::computeDistanceWithLatLng($locLng, $locLat, $uniLng, $uniLat, 1, 0);
                if ($dis < $radius) {
                    return [
                        'uniqueList' => $uniqueList,
                        'ret' => false,
                    ];
                }
            }
        }
        $uniqueList[] = [
            'lng' => $locLng,
            'lat' => $locLat,
        ];

        return [
            'uniqueList' => $uniqueList,
            'ret' => true,
        ];
    }

    public function addUserModelLoc($userId, $userModelId,
                                    $locId, $amapPoiId, $storyId, $sessionId,
                                    $storyModelId, $storyStageId, $activeClass, $userModelProp, $userModelLocStatus = UserModelLoc::USER_MODEL_LOC_STATUS_LIVE) {
        $userModelLoc = new UserModelLoc();
        $userModelLoc->user_id = $userId;
        $userModelLoc->user_model_id = $userModelId;
        $userModelLoc->location_id = $locId;
        $userModelLoc->story_model_id = $storyModelId;
        $userModelLoc->story_stage_id = $storyStageId;
        $userModelLoc->active_class = $activeClass;
        $userModelLoc->story_id = $storyId;
//        $userModelLoc->session_id = $sessionId;
        $userModelLoc->amap_poi_id = $amapPoiId;
        $userModelLoc->user_model_prop = json_encode(['prop' => $userModelProp]);
        $userModelLoc->user_model_loc_status = $userModelLocStatus;
        $userModelLoc->save();
        $userModelLoc->id = \Yii::$app->db->getLastInsertID();

        return $userModelLoc;
    }

    public function getUserModelLoc($userLng, $userLat, $radius = 1000, $limit = 30) {
        $locationsRet = Yii::$app->location->getLocationFromDbAndAMap($userLng, $userLat, $radius, $limit);

        $locations = [];
        $locationIds = [];
        if (!empty($locationsRet)) {
            foreach ($locationsRet as $loc) {
                $locationIds[] = $loc['id'];
                $locations[$loc['id']] = $loc;
            }
        }

        sort($locationIds);

        $userModelLocs = $this->getUserModelLocByLocIds($locationIds, UserModelLoc::USER_MODEL_LOC_STATUS_LIVE);

        $userModelLocsKv = [];
        $storyModelsKv = [];
        if (!empty($userModelLocs)) {
            foreach ($userModelLocs as $userModelLoc) {
                $userModelLocsKv[$userModelLoc->location_id][] = $userModelLoc;
                $storyModelsKv[$userModelLoc->story_model_id][] = $userModelLoc;
            }
        }

        return [
            'locations' => $locations,
            'locationIds' => $locationIds,
            'userModelLocs' => $userModelLocs,
            'userModelLocsKv' => $userModelLocsKv,
            'storyModelsKv' => $storyModelsKv,
        ];
    }

    public function getUserModelLocByLocIds($locationIds, $userModelLocStatus = []) {
        $userModelLocs = UserModelLoc::find()
            ->with('storyModel')
            ->where([
                'location_id' => $locationIds,
            ]);
        if (!empty($userModelLocStatus)) {
            $userModelLocs->andFilterWhere([
                'user_model_loc_status' => $userModelLocStatus,
            ]);
        }
        $userModelLocs = $userModelLocs->all();

        return $userModelLocs;
    }

    public function getUserModelLocByUserId($userId, $userModelLocStatus = []) {
        $userModelLocs = UserModelLoc::find()
            ->where([
                'user_id' => $userId,
            ]);
        if (!empty($userModelLocStatus)) {
            $userModelLocs->andFilterWhere([
                'user_model_loc_status' => $userModelLocStatus,
            ]);
        }
        $userModelLocs = $userModelLocs->all();

        return $userModelLocs;
    }

}