<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\parent;


use common\definitions\Common;
use common\definitions\Cookies;
use common\definitions\ErrorCode;
use common\helpers\Active;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\helpers\Model;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Knowledge;
use common\models\Log;
use common\models\Order;
use common\models\Qa;
use common\models\QaPackage;
use common\models\Session;
use common\models\SessionModels;
use common\models\SessionQa;
use common\models\SessionStages;
use common\models\ShopWares;
use common\models\Story;
use common\models\StoryExtend;
use common\models\StoryGoal;
use common\models\StoryModels;
use common\models\StoryModelsLink;
use common\models\StoryRank;
use common\models\StoryRole;
use common\models\StoryStages;
use common\models\User;
use common\models\UserData;
use common\models\UserExtends;
use common\models\UserKnowledge;
use common\models\UserModelLoc;
use common\models\UserModelsUsed;
use common\models\UserQa;
use common\models\UserScore;
use common\models\UserStory;
use common\models\UserModels;
use frontend\actions\ApiAction;
use frontend\actions\order\Exception;
use Yii;

class ParentApi extends ApiAction
{
    public $action;
    private $_get;
    private $_userId;


    public function run()
    {

        try {
            $this->valToken();

            $this->_get = Yii::$app->request->get();

            $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

            switch ($this->action) {
                case 'get_shop_wares':
                    $ret = $this->getShopWares();
                    break;
                case 'get_one_shop_ware':
                    $ret = $this->getOneShopWare();
                    break;
                case 'get_data':
                    $ret = $this->getData();
                    break;
                case 'get_orders':
                    $ret = $this->getOrders();
                    break;
                case 'get_subjects_his_by_user':
                    $ret = $this->getSubjectsHisByUser();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            var_dump($e);
            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $this->success($ret);
    }

    /**
     * 获取剧本信息
     */
    public function getData() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;

        $user = User::findOne($userId);
        $userScore = UserScore::find()
            ->where([
                'user_id' => $userId,
            ])
            ->one();

        $userExtends = UserExtends::find()
            ->where([
                'user_id' => $userId,
            ])
            ->one();

//        if (!empty($user)) {
            $userInfo['username'] = !empty($user->user_name) ? $user->user_name : $user->mobile;
            $userInfo['avatar'] = !empty($user->avatar) ? Attachment::completeUrl($user->avatar, true) : ' - ';
            $userInfo['nickname'] = !empty($user->nick_name) ? $user->nick_name : '';
            $userInfo['mobile'] = !empty($user->mobile) ? $user->mobile : ' - ';
            $userInfo['last_login_time'] = Date('Y-m-d H:i', $user->last_login_time);
//        }
        $userInfo['score'] = !empty($userScore->score) ? $userScore->score : 0;

        $userInfo['level'] = !empty($userExtends->level) ? $userExtends->level : 0;
        $userInfo['grade'] = !empty($userExtends->grade) ? $userExtends->grade : 0;
        $userInfo['grade_name'] = !empty(UserExtends::$userGrade2Name[$userInfo['grade']]) ? UserExtends::$userGrade2Name[$userInfo['grade']] : '';

//        $todayQas = UserQa::find()
//            ->where([
//                'user_id' => $userId,
//            ])
//            ->andFilterWhere([
//                'BETWEEN', 'created_at', strtotime(date('Y-m-d 00:00:00')), strtotime(date('Y-m-d 23:59:59'))
//            ])
//            ->orderBy(['id' => SORT_DESC])
//            ->all();
//
//        $todayRight = 0;
//        $todayWrong = 0;
//        $todayTotal = 0;
//        $todayRate = 0;
//        $todayTime = 0;
//        if (!empty($todayQas)) {
//            foreach ($todayQas as $qa) {
//                $todayTotal++;
//                if ($qa->is_right == UserQa::ANSWER_RIGHT) {
//                    $todayRight++;
//                } else {
//                    $todayWrong++;
//                }
//            }
//            $todayRate = round($todayRight / $todayTotal * 100, 2);
//        }

//        $total = UserQa::find()
//            ->where([
//                'user_id' => $userId,
//            ])
//            ->count();

        $totalCount = UserData::find()
            ->where([
                'story_id'  => 5,
                'data_type' => UserData::DATA_TYPE_TOTAL,
            ])
            ->count();

        $todayCount = UserData::find()
            ->where([
                'story_id'  => 5,
                'data_type' => UserData::DATA_TYPE_TOTAL,
            ])
            ->andFilterWhere([
                'BETWEEN', 'created_at', strtotime(date('Y-m-d 00:00:00')), strtotime(date('Y-m-d 23:59:59'))
            ])
            ->count();

        $userDatas = UserData::find()
            ->where([
                'user_id'   => $userId,
                'story_id'  => 5,
            ])
            ->andFilterWhere([
                'or',
                ['time_type' => UserData::USER_DATA_TIME_TYPE_TOTAL],
                [
                    'AND',
                    ['time_type' => UserData::USER_DATA_TIME_TYPE_DAY],
                    ['BETWEEN', 'created_at', strtotime(date('Y-m-d 00:00:00')), strtotime(date('Y-m-d 23:59:59'))]
                ],
            ])
            ->all();

        $datas = [];
        if (!empty($userDatas)) {
            foreach ($userDatas as $userData) {
                $datas[$userData->data_type] = $userData->data_value;
                switch ($userData->time_type) {
                    case UserData::USER_DATA_TIME_TYPE_TOTAL:
                        $rankCount = UserData::find()
                            ->where([
                                'story_id' => 5,
                                'data_type' => UserData::DATA_TYPE_TOTAL,
                                'time_type' => UserData::USER_DATA_TIME_TYPE_TOTAL,
                            ])
                            ->andWhere(['>', 'data_value', $userData->data_value])
                            ->count();
                        if ($rankCount == 0 && $totalCount > 0) {
                            $rankRate = 100;
                        } else {
                            $rankRate = intval($rankCount / $totalCount);
                        }
                        break;
                    case UserData::USER_DATA_TIME_TYPE_DAY:
                    default:
                        $tag = '>';
                        switch ($userData->data_type) {
                            case UserData::DATA_TYPE_TODAY_WRONG:
                                $tag = '<';
                                break;
                            default:
                                $tag = '>';
                                break;
                        }
                        $rankCount = UserData::find()
                            ->where([
                                'story_id' => 5,
                                'data_type' => UserData::DATA_TYPE_TODAY_TOTAL,
                                'time_type' => UserData::USER_DATA_TIME_TYPE_DAY,
                            ])
                            ->andFilterWhere([
                                'BETWEEN', 'created_at', strtotime(date('Y-m-d 00:00:00')), strtotime(date('Y-m-d 23:59:59'))
                            ])
                            ->andFilterWhere([$tag, 'data_value', (int)$userData->data_value])
//                            ->createCommand()
//                            ->getRawSql();
//                        var_dump($rankCount);exit;
                            ->count();
                        if ($rankCount == 0 && $todayCount > 0) {
                            $rankRate = 100;
                        } else {
                            $rankRate = intval($rankCount / $todayCount * 100);
                        }
                        break;
                }
                $descs[$userData->data_type] = '超越了' . $rankRate . '%同级学子';
            }
        }

        if (!empty($datas[UserData::DATA_TYPE_TODAY_TOTAL]) && !empty($datas[UserData::DATA_TYPE_TODAY_RIGHT])) {
            $datas[UserData::DATA_TYPE_TODAY_RATE] = round($datas[UserData::DATA_TYPE_TODAY_RIGHT] / $datas[UserData::DATA_TYPE_TODAY_TOTAL] * 100, 2) . '%';
        } else {
            $datas[UserData::DATA_TYPE_TODAY_RATE] = '0%';
        }

        $cols = [
            'total' => UserData::DATA_TYPE_TOTAL,
            'today_total' => UserData::DATA_TYPE_TODAY_TOTAL,
            'today_right' => UserData::DATA_TYPE_TODAY_RIGHT,
            'today_wrong' => UserData::DATA_TYPE_TODAY_WRONG,
            'today_rate' => UserData::DATA_TYPE_TODAY_RATE,
        ];

        $data = [];

        foreach ($cols as $col) {
            if (!empty($datas[$col])) {
                $data[] = [
                    'title' => UserData::$dataType2Name[$col],
                    'content' => $datas[$col],
                    'desc' => !empty($descs[$col]) ? $descs[$col] : '',
                ];
            } else {
                $data[] = [
                    'title' => UserData::$dataType2Name[$col],
                    'content' => 0,
                    'desc' => '',
                ];
            }
        }


//            [
//                'title' => '总耗时(小时)',
//                'content'=> rand(1,3),
//            ],
//            [
//                'title' => '平均每道题时长(秒)',
//                'content' => rand(8,20),
//            ],


        $ret = [
            'user' => $userInfo,
            'data' => $data,
        ];

        return $ret;
    }

    public function getOneShopWare() {
        $shopWareId = !empty($_GET['shop_ware_id']) ? $_GET['shop_ware_id'] : 0;

        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;

        $model = ShopWares::find()->where(['id' => $shopWareId])->one();
        $model = $model->toArray();

//        var_dump($model);exit;

        $hasBought = false;
        $boughtOrder = null;
        if (!empty($model)) {
            switch ($model['link_type']) {
                case ShopWares::LINK_TYPE_QA_PACKAGE:
                    $model['qa_package'] = QaPackage::findOne($model['link_id']);
                    break;
                case ShopWares::LINK_TYPE_STORY_MODEL:
                default:
                    $model['story'] = StoryModels::findOne($model['link_id']);
                    break;
            }

            $model['icon'] = Attachment::completeUrl($model['icon'], true);

            $order = Order::find()
                ->where([
                    'user_id' => $userId,
                    'item_id' => $shopWareId,
                    'item_type' => Order::ITEM_TYPE_PACKAGE,
                ])
                ->orderBy(['id' => SORT_DESC])
                ->one();

            if (!empty($order)) {
                $order = $order->toArray();
                $orderCreatedAt = $order['created_at'];
                $order['order_status_name'] = !empty(Order::$orderStatus[$order['order_status']])
                    ? Order::$orderStatus[$order['order_status']] : '';

                $period = $model['period'];

                if ($period > 0) {
                    $expireTime = $orderCreatedAt + $period * 24 * 3600;
                    if (time() < $expireTime) {
                        $hasBought = true;
                        $boughtOrder = $order;
                    }
                }
            }

        }

        $model['has_bought'] = $hasBought;
        $model['bought_order'] = $boughtOrder;

        return $model;
    }

    public function getShopWares() {

        $shopWareType = !empty($_GET['shop_ware_type']) ? $_GET['shop_ware_type'] : ShopWares::SHOP_WARE_TYPE_GAME_ITEM;

        $storyModelClass = !empty($_GET['story_model_class']) ? $_GET['story_model_class'] : '';

        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $limit = 10;

        $model = ShopWares::find()
            ->where([
                'ware_type' => $shopWareType,
                'is_delete' => Common::STATUS_NORMAL,
            ]);
            if (!empty($storyModelClass)) {
                $model = $model->andFilterWhere(['o_story_model.story_model_class' => $storyModelClass]);
            }
            $model = $model->orderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();

        if (!empty($model)) {
            foreach ($model as &$data) {
                switch ($data['link_type']) {
                    case ShopWares::LINK_TYPE_QA_PACKAGE:
                        $data['qa_package'] = QaPackage::findOne($data['link_id']);
                        break;
                    case ShopWares::LINK_TYPE_STORY_MODEL:
                    default:
                        $data['story'] = StoryModels::findOne($data['link_id']);
                        break;
                }

                $data['icon'] = Attachment::completeUrl($data['icon'], true);
            }
        }

        return $model;
    }

    public function getOrders() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;

        $itemType = !empty($_GET['item_type']) ? $_GET['item_type'] : 0;
        $orderStatus = !empty($_GET['order_status']) ? $_GET['order_status'] : 0;

        $orders = Order::find()
            ->where([
                'user_id' => $userId,
            ]);
        if (!empty($itemType)) {
            $orders = $orders->andFilterWhere(['item_type' => $itemType]);
        }
        if (!empty($orderStatus)) {
            $orders = $orders->andFilterWhere(['order_status' => $orderStatus]);
        }
        $orders = $orders->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        if (!empty($orders)) {
            foreach ($orders as &$order) {
                if (!empty($order['item_id'])) {
                    switch ($order['item_type']) {
                        case Order::ITEM_TYPE_STORY:
                            $order['story'] = Story::findOne($order['item_id']);
                            break;
                        case Order::ITEM_TYPE_PACKAGE:
                            $order['shop_ware'] = ShopWares::findOne($order['item_id']);
                            break;
                    }
                }

                $order['user'] = User::findOne($order['user_id']);
                $order['order_status_name'] = !empty(Order::$orderStatus[$order['order_status']])
                        ? Order::$orderStatus[$order['order_status']] : '';
            }
        }

        return $orders;
    }

    public function getSubjectsHisByUser() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;

        $isRight = !empty($_GET['is_right']) ? $_GET['is_right'] : UserQa::ANSWER_WRONG;       // 1 - 正确； 2 - 错误； 0 - 全部

        $model = UserQa::find()
            ->where([
                'user_id' => $userId,
            ]);
        if (!empty($isRight)) {
            $model = $model->andFilterWhere(['is_right' => $isRight]);
        }
        $model = $model->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        if (!empty($model)) {
            foreach ($model as &$data) {
                $data['qa'] = Qa::findOne($data['qa_id']);
//                $data['story'] = Story::findOne($data['story_id']);
                $data['user'] = User::findOne($data['user_id']);
            }
        }

        return $model;
    }



}