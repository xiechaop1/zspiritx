<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\models\SessionModels;
use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class Location extends Component
{

    public function addOrUpdateLocation($amapPoiId, $locationName, $locationType, $lat, $lng,
                                        $address, $businessArea, $adcode, $tel, $aoiType,
                                        $amapRet, $resource = 'amap') {
        $location = \common\models\Location::find()
            ->where([
                'amap_poi_id' => $amapPoiId,
            ])
            ->one();

        if (empty($location)) {

            $location = new \common\models\Location();
            $location->amap_poi_id = $amapPoiId;
            $location->location_name = $locationName;
            $location->location_type = $locationType;
            $location->lng = $lng;
            $location->lat = $lat;
            $location->address = $address;
            $location->businessarea = $businessArea;
            $location->adcode = $adcode;
            $location->tel = $tel;
            $location->aoi_type = $aoiType;
            $location->amap_ret = $amapRet;
            $location->resource = $resource;
            $location->save();

            $location->id = \Yii::$app->db->getLastInsertID();
        } else {
            if (!empty($locationName)) {
                $location->location_name = $locationName;
            }
            if (!empty($locationType)) {
                $location->location_type = $locationType;
            }
            if (!empty($address)) {
                $location->address = $address;
            }
            if (!empty($businessArea)) {
                $location->businessarea = $businessArea;
            }
            if (!empty($adcode)) {
                $location->adcode = $adcode;
            }
            if (!empty($tel)) {
                $location->tel = $tel;
            }
            if (!empty($aoiType)) {
                $location->aoi_type = $aoiType;
            }
            if (!empty($amapRet)) {
                $location->amap_ret = $amapRet;
            }
            if (!empty($resource)) {
                $location->resource = $resource;
            }
            if (!empty($lng)) {
                $location->lng = $lng;
            }
            if (!empty($lat)) {
                $location->lat = $lat;
            }

            $location->save();
        }

        return $location;
    }


    public function getLocationsByLngLat($userLng, $userLat, $radius = 1000, $limit = 30, $offset = 0, $poitype = '') {
        $sql = 'SELECT *, st_distance(point(lng, lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 as dist FROM o_location';
        if (!empty($poitype)) {
            $sql .= ' AND poi_type like "%' . $poitype . '%"';
        }
        $sql .= ' HAVING dist < ' . $radius;
        $sql .= ' ORDER BY dist ASC;';
        $sql .= ' LIMIT ' . $offset . ', ' . $limit;

        $ret = \Yii::$app->db->createCommand($sql)->queryAll();

        return $ret;
    }

    public function addOrUpdateLocationByAMap($amapRetJson) {
        if (empty($amapRetJson)) {
            return false;
        }
//        $amapRet = json_decode($amapRetJson, true);
        $amapRet = $amapRetJson;
        if (empty($amapRet['regeocode'])
            || empty($amapRet['regeocode']['pois'])
        ) {
            return false;
        }
        
        $pois = $amapRet['regeocode']['pois'];
        $aois = !empty($amapRet['regeocode']['aois']) ? $amapRet['regeocode']['aois'] : [];
        unset($amapRet['regeocode']['pois']);
//        unset($amapRet['regeocode']['aois']);
        $amapRetSave = json_encode($amapRet);

        if (!empty($pois)) {
            foreach ($pois as $poi) {
                $poiLocation = explode(',', $poi['location']);
                $this->addOrUpdateLocation(
                    $poi['id'],
                    $poi['name'],
                    $poi['type'],
                    $poiLocation[1],
                    $poiLocation[0],
                    $poi['address'],
                    !empty($poi['businessarea']) ? $poi['businessarea'] : '',
                    !empty($poi['adcode']) ? $poi['adcode'] : '',
                    !empty($poi['tel']) ? json_encode($poi['tel']) : '',
                    '',
                    $amapRetSave
                );
            }
        }

        if (!empty($aois)) {
            foreach ($aois as $aoi) {
                $poiId = $aoi['id'];
                $aoiType = $aoi['type'];
                $this->addOrUpdateLocation(
                    $poiId,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $aoiType,
                    ''
                );
            }
        }

        return true;


    }

    public function getLocationFromDbAndAMap($userLng, $userLat, $radius = 1000, $limit = 30, $offset = 0, $poitype = '') {
//        $dbRet = $this->getLocationsByLngLat($userLng, $userLat, $radius);
        $amapRet = Yii::$app->amap->getAMapReGeoCodeByLngLat($userLng, $userLat, '', $radius);

        if (!empty($amapRet)) {
            $this->addOrUpdateLocationByAMap($amapRet);
        }

        $dbRet = $this->getLocationsByLngLat($userLng, $userLat, $radius, $limit, $offset, $poitype);

        return $dbRet;

    }

}