<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\services\Curl;
use common\models\User;
use yii\base\Component;
use yii;

class AMap extends Component
{

    const AMAP_HOST = 'https://restapi.amap.com';

    public $appKey;

    public function getAMapReGeoCodeByLngLat($lng, $lat, $poitype = '', $radius = 1000, $extensions = 'all', $roadlevel = 0) {
        $uri = '/v3/geocode/regeo';

        $params = [
            'key'       => $this->appKey,
            'location'  => $lng . ',' . $lat,
            'poitype'   => $poitype,
            'radius'    => $radius,
            'extensions'    => $extensions,
            'roadlevel' => $roadlevel,
        ];

        $uri = $this->_createUri($uri, self::AMAP_HOST, $params);

        try {
            $ret = $this->_getApi($uri);
        } catch (\Exception $e) {
            throw new \Exception('获取数据失败：' . $e->getMessage(), $e->getCode());
        }

        return $ret;
    }


    private function _createUri($uri, $host, $params = []) {
        $uri = $host . $uri;
        if (!empty($params)) {
            $uri .= '?' . http_build_query($params);
        }
        return $uri;
    }

    private function _getApi($uri) {
        $ret = Curl::curlGet($uri);
        $ret = json_decode($ret, true);
        if (!empty($ret['errcode']) && $ret['errcode'] != 0) {
            throw new \Exception($ret['errmsg'], $ret['errcode']);
        }
        return $ret;
    }

    private function _getPostApi($uri, $postParams = []) {
        $ret = Curl::curlPost($uri, $postParams, [], true);
        $ret = json_decode($ret, true);
        if (!empty($ret['errcode']) && $ret['errcode'] != 0) {
            throw new \Exception($ret['errmsg'], $ret['errcode']);
        }
        return $ret;
    }

}