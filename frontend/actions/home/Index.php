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
use common\helpers\QQWry;
use common\models\Order;
use common\models\Story;
use common\models\User;
use common\models\UserStory;
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
        $beginTs = time();
        $image = 'img/home/index_image.jpg';
        $image = Attachment::completeUrl($image, false);
        $unityVersion = !empty($_GET['unity_version']) ? $_GET['unity_version'] : '';

        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $defStoryId = 5;

        // 实现一段代码，判断用户的IP是不是在香港地区
        $userIp = Yii::$app->request->userIP;
        
        // if (!empty($userIp) && $userIp !== '127.0.0.1' && $userIp !== '::1') {
        //     try {
        //         // 使用免费的IP地理位置API
        //         $apiUrl = "http://ip-api.com/json/{$userIp}?fields=status,message,country,regionName,city";
        //         $response = file_get_contents($apiUrl);
        //         $ipData = json_decode($response, true);
                
        //         if ($ipData && $ipData['status'] === 'success') {
        //             $isHongKong = ($ipData['country'] === 'Hong Kong');
        //             Yii::info("IP: {$userIp}, Country: {$ipData['country']}, City: {$ipData['city']}, IsHK: " . ($isHongKong ? 'true' : 'false'));
        //         }
        //     } catch (\Exception $e) {
        //         Yii::error("IP地理位置查询失败: " . $e->getMessage());
        //     }
        // }
        
        // 如果需要更精确的判断，可以使用以下备选方案
        $isHongKong = \common\helpers\Common::checkPosByIP($userIp, '香港');
        if ($isHongKong) {
            $defStoryId = 16;       //  坚尼地城的剧本ID
        }

//        $banner = [
//            'zhuluoji' => Attachment::completeUrl('img/home/konglong2.jpg', true),
//            'taoranting' => Attachment::completeUrl('img/home/taoranting1.jpg', true),
//            'senlin' => Attachment::completeUrl('img/home/index_image.jpg', true),
//        ];

//        $userId = !empty($_SESSION['user_info']['id']) ? $_SESSION['user_info']['id'] : 0;

        $userId = Cookie::getCookie('user_id');
        if (empty($userId)) {
            $params = !empty($unityVersion) ? '?unity_version=' . $unityVersion : '';
            header('Location: /passport/web_login' . $params);
        }

        try {
            $user = User::find()
                ->where(['id' => $userId])
                ->one();

            if ($user->user_status == User::USER_STATUS_NORMAL) {

                $user->last_login_time = time();
                $user->last_login_device = Client::getAgent();
                $user->save();
            } else {
                header('Location: /passport/web_login' . !empty($unityVersion) ? '?unity_version=' . $unityVersion : '');
            }

            if (strlen($user->mobile) <= 4) {
                $user->user_type = User::USER_TYPE_INNER;
            } else {
                if ($defStoryId != 16) {
                    // Todo：临时方案，香港目前只有一个剧本，因此香港之外的地区再执行进入上次剧本
                    $userStory = UserStory::find()
                        ->where([
                            'user_id' => $userId
                        ])
                        ->orderBy(['updated_at' => SORT_DESC])
                        ->one();

                    if (!empty($userStory->story_id)) {
                        $defStoryId = $userStory->story_id;
                    }
                }
            }
        } catch (\Exception $e) {
            //Yii::error($e->getMessage());
        }

        $stories = Story::find();
        if (!empty($storyId)) {
            $stories = $stories->where(['id' => $storyId]);
        }
        $stories = $stories
            ->andFilterWhere(['story_status' => [Story::STORY_STATUS_ONLINE, Story::STORY_STATUS_OPEN_WAIT]])
            ->orderBy(['sort_by' => SORT_ASC])
            ->all();

        $orders = Order::find()
            ->where([
                'user_id'   => $userId,
                'item_type' => Order::ITEM_TYPE_STORY,
            ])
            ->all();

        $ordersMap = [];
        foreach ($orders as $order) {
            $ordersMap[$order->story_id] = $order->order_status;
        }

        $bgm = '';

        return $this->controller->render('index', [
            'userId'    => $userId,
            'user'      => $user,
            'stories'   => $stories,
            'orders'    => $orders,
            'ordersMap' => $ordersMap,
            'voice' => '',
            'image' => $image,
            'bgSound' => $bgm,
            'unityVersion' => $unityVersion,
            'beginTs' => $beginTs,
            'storyId' => $storyId,
            'defStoryId' => $defStoryId,

        ]);
    }
    
    /**
     * 使用本地IP库判断是否在香港（备选方案）
     * 需要下载IP库文件到指定路径
     * @param string $ip
     * @return bool
     */
    private function checkHongKongByIP($ip, $tag = '香港')
    {
        // 方法1：使用纯真IP库（需要下载qqwry.dat文件）
        $ipFile = Yii::getAlias('@common/data/qqwry.dat');
        if (file_exists($ipFile)) {
            try {
                // 这里需要引入纯真IP库的查询类
                $location = QQWry::query($ip, $ipFile);
                if ($location && strpos($location, $tag) !== false) {
                    return true;
                }
                // return strpos($location, '香港') !== false;
            } catch (\Exception $e) {
                // Yii::error("纯真IP库查询失败: " . $e->getMessage());
            }
            return false;
        }

        return false;
    }
}