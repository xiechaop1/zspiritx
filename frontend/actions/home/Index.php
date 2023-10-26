<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\home;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Cookie;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Index extends Action
{

    
    public function run()
    {
//var_dump($_SESSION);

        $image = 'img/home/index_image.jpg';
        $image = Attachment::completeUrl($image, true);

//        $banner = [
//            'zhuluoji' => Attachment::completeUrl('img/home/konglong2.jpg', true),
//            'taoranting' => Attachment::completeUrl('img/home/taoranting1.jpg', true),
//            'senlin' => Attachment::completeUrl('img/home/index_image.jpg', true),
//        ];

//        $userId = !empty($_SESSION['user_info']['id']) ? $_SESSION['user_info']['id'] : 0;

        $userId = Cookie::getCookie('user_id');
        if (empty($userId)) {
            header('Location: /passport/weblogin');
        }

        $stories = Story::find()
            ->where(['story_status' => Story::STORY_STATUS_ONLINE])
            ->orderBy(['sort_by' => SORT_ASC])
            ->all();

        return $this->controller->render('index', [
            'userId'    => $userId,
            'stories'   => $stories,
            'voice' => '',
            'image' => $image,
//            'banner' => $banner,
        ]);
    }
}