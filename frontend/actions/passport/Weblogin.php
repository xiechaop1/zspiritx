<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/1
 * Time: 3:13 PM
 */

namespace frontend\actions\passport;


use yii\base\Action;
use yii;

class Weblogin extends Action
{

    public function run()
    {

//        $params = [
//            'activity_position' => HewaApi::BANNER_POSITION_LOGIN,
//            'check_time'        => strtotime('now'),
//            'banner_status'     => HewaApi::BANNER_STATUS_OPEN,
//        ];
//        $bannerData = Yii::$app->hewaApi->getBanner($params);

//        $banners = !empty($bannerData['data']) ? $bannerData['data'] : [];

        $refUrl = Yii::$app->request->get('ref');


        return $this->controller->render('login', [
//            'banners'       => $banners,
//            'ref_url'       => $refUrl,
//            'source'        => Yii::$app->request->get('source', ''),
        ]);

    }
}