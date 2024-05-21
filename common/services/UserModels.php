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

    public function setUserModelToLoc($storyId, $sessionId, $userLng, $userLat, $radius = 1000, $limit = 30) {
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

                    $storyModels = StoryModels::find()
                        ->where([
                            'story_model_class' => StoryModels::STORY_MODEL_CLASS_PET
                        ])
                        ->orderBy('rand()')
                        ->limit(10)
                        ->all();

                    if (!empty($storyModels)) {
                        foreach ($storyModels as $storyModel) {

                            $propArray = Model::getUserModelProp($storyModel, 'story_model_prop');
                            $formula = !empty($propArray['init_formula']) ? $propArray['init_formula'] : '';

                            if (!empty($formula)) {
                                eval('$userModelProp = ' . $formula . ';');
                            } else {
                                $userModelProp = [];
                            }

                            $amapPoiId = !empty($locations[$locId]['amap_poi_id']) ? $locations[$locId]['amap_poi_id'] : '';
                            $userModelLoc = $this->addUserModelLoc(0, 0,
                                $locId, $amapPoiId, $storyId, $sessionId,
                                $storyModel->id, $userModelProp);
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
                                'userModelLoc' => $userModelLoc,
                                'location' => $locations[$locId],
                            ];
//                            $userModelLoc;
                            $storyModelsResult[$storyModel->id][] = [
                                'userModelLoc' => $userModelLoc,
                                'location' => $locations[$locId],
                            ];
//                            $userModelLoc;
                        }
                    }

                }
            }
        }
        return [
            'result' => $result,
            'storyModelsResult' => $storyModelsResult,
        ];

    }

    public function addUserModelLoc($userId, $userModelId,
                                    $locId, $amapPoiId, $storyId, $sessionId,
                                    $storyModelId, $userModelProp, $userModelLocStatus = UserModelLoc::USER_MODEL_LOC_STATUS_LIVE) {
        $userModelLoc = new UserModelLoc();
        $userModelLoc->user_id = $userId;
        $userModelLoc->user_model_id = $userModelId;
        $userModelLoc->location_id = $locId;
        $userModelLoc->story_model_id = $storyModelId;
        $userModelLoc->story_id = $storyId;
//        $userModelLoc->session_id = $sessionId;
        $userModelLoc->amap_poi_id = $amapPoiId;
        $userModelLoc->user_model_prop = $userModelProp;
        $userModelLoc->user_model_loc_status = $userModelLocStatus;
        $userModelLoc->save();
        $userModelLoc->id = \Yii::$app->db->getLastInsertID();

        return $userModelLoc;
    }

    public function getUserModelLoc($userLng, $userLat, $radius = 1000, $limit = 30) {
        $locations = Yii::$app->location->getLocationFromDbAndAMap($userLng, $userLat, $radius, $limit);

        $locationIds = [];
        if (!empty($locations)) {
            foreach ($locations as $loc) {
                $locationIds[] = $loc['id'];
            }
        }

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

}