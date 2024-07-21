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
//            var_dump($e);
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

        $ret = [
            'user' => $user,
            'user_score' => $userScore,
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
                $orderCreatedAt = $order->created_at;

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