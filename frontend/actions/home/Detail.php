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
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\Order;
use common\models\Story;
use common\models\User;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Detail extends Action
{

    
    public function run()
    {

        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $story = Story::find()
            ->where(['id' => $storyId])
            ->one();

//        $storyExtend = $story->extend;


        $userId = Cookie::getCookie('user_id');
        if (empty($userId)) {
            header('Location: /passport/web_login');
        }

        $user = User::find()
            ->where(['id' => $userId])
            ->one();

        $order = Order::find()
            ->where([
                'user_id'   => $userId,
                'story_id'  => $storyId,
            ])
            ->one();

        $fileName = dirname(__FILE__) . '/../../web/' . $storyId . '.txt';
        if (!file_exists($fileName)) {
            $guide = [];
        } else {
            $content = file_get_contents($fileName);
            if (!empty($content)) {
                $guide = explode('---', $content);
            } else {
                $guide = [];
            }
        }


        return $this->controller->render('detail', [
            'userId'    => $userId,
            'user'      => $user,
            'guide'     => $guide,
            'story'   => $story,
            'order'    => $order,
            'voice' => '',

//            'banner' => $banner,
        ]);
    }
}