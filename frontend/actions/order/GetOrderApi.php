<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\order;


use common\definitions\ErrorCode;
use common\models\Order;
use common\models\Story;
use common\models\StoryExtend;
use common\models\User;
use frontend\actions\ApiAction;
use Yii;

class GetOrderApi extends ApiAction
{
    public $action;
    private $_get;
    private $_userId;

    private $_userInfo;

    public function run()
    {

        try {
            $this->valToken();

            $this->_get = Yii::$app->request->get();

            $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

            if (empty($this->_userId)) {
                return $this->fail('请您给出用户信息', ErrorCode::USER_NOT_FOUND);
            }

            $this->_userInfo = User::findOne($this->_userId);

            switch ($this->action) {
                case 'get_order_list':
                    $ret = $this->getOrderList();
                    break;
                case 'get_order':
                    $ret = $this->getOrder();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), $e->getCode());
        }

        return $this->success($ret);
    }


    /**
     * 创建订单
     * @return array
     * @throws \yii\db\Exception
     */

    public function getOrderList() {

        $orderData = Order::find()
            ->where([
                'user_id' => $this->_userId,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $ret = [];
        if (!empty($orderData)) {
            foreach ($orderData as $order) {
                $story = [];
                if (!empty($order->story)) {
                    $story = $order->story;
                }
                $storyExtend = [];
                if (!empty($story->extend)) {
                    $storyExtend = $story->extend;
                }
                $ret[] = [
                    'order' => $order,
                    'story' => $story,
                    'storyExtend' => $storyExtend,
                ];
            }
        }


        return $ret;
    }

    public function getOrder() {

        $orderId = !empty($this->_get['order_id']) ? $this->_get['order_id'] : 0;

        $order = Order::find()
            ->where([
                'id' => $orderId,
                'user_id' => $this->_userId,
            ])
            ->one();

        if (empty($order)) {
            return [];
        }

        return $order;

    }



}