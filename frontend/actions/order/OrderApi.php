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

class OrderApi extends ApiAction
{
    public $action;
    private $_get;
    private $_storyId;
    private $_userId;

    private $_storyInfo;

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
                case 'create':
                    $ret = $this->create();
                    break;
                case 'success':
                    $ret = $this->success();
                    break;
                case 'pay':
                    $ret = $this->pay();
                    break;
                case 'cancel':
                    $ret = $this->cancel();
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

    public function create() {

        if (empty($this->_get['story_id'])) {
            return $this->fail('请您给出剧本信息', ErrorCode::STORY_NOT_FOUND);
        } else {
            $this->_storyId = $this->_get['story_id'];

            // 检查剧本是否存在
            $this->_storyInfo = Story::findOne($this->_storyId);
            if (empty($this->_storyInfo)) {
                return $this->fail('剧本不存在', ErrorCode::STORY_NOT_FOUND);
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $storyExtend = StoryExtend::findOne(['story_id' => $this->_storyId]);

            if (empty($storyExtend)) {
                throw new Exception('剧本不存在', ErrorCode::STORY_NOT_FOUND);
            }

            $currPrice = $storyExtend['curr_price'];

            $payMethod = 1;     // 微信支付

            $order = new Order();
            $order->user_id = $this->_userId;
            $order->story_id = $this->_storyId;
            $order->order_no = \common\helpers\Order::generateOutTradeNo($this->_userId, $this->_storyId, $payMethod);
            $order->amount = $currPrice;
            $order->story_price = $storyExtend['price'];

            if ($currPrice > 0) {
                $order->order_status = Order::ORDER_STATUS_WAIT;
                $order->expire_time = time() + 30 * 60;     // 30分钟过期
            } else {
                $order->order_status = Order::ORDER_STATUS_PAIED;
            }
            $ret = $order->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $order;
    }

    public function pay() {
        $transaction = Yii::$app->db->beginTransaction();

        if (empty($this->_get['order_id'])) {
            throw new Exception('请您给出订单信息', ErrorCode::ORDER_NOT_FOUND);
        }

        $orderId = $this->_get['order_id'];

        $order = Order::findOne($orderId);

        if (empty($order)) {
            throw new Exception('订单不存在', ErrorCode::ORDER_NOT_FOUND);
        }

        if ($order->order_status != Order::ORDER_STATUS_WAIT) {
            throw new Exception('订单状态不正确', ErrorCode::ORDER_STATUS_ERROR);
        }
        try {
            $order->order_status = Order::ORDER_STATUS_PAIED;
            $ret = $order->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $order;
    }

    public function cancel() {

        $transaction = Yii::$app->db->beginTransaction();

        if (empty($this->_get['order_id'])) {
            throw new Exception('请您给出订单信息', ErrorCode::ORDER_NOT_FOUND);
        }

        $orderId = $this->_get['order_id'];

        $order = Order::findOne($orderId);

        if (empty($order)) {
            throw new Exception('订单不存在', ErrorCode::ORDER_NOT_FOUND);
        }

        if ($order->order_status != Order::ORDER_STATUS_PAIED) {
            throw new Exception('订单状态不正确', ErrorCode::ORDER_STATUS_ERROR);
        }
        try {
            $order->order_status = Order::ORDER_STATUS_CANCELED;
            $ret = $order->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $order;

    }

}