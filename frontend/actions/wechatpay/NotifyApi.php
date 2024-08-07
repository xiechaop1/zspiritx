<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\wechatpay;


use common\definitions\ErrorCode;
use common\models\Order;
use common\models\Story;
use common\models\StoryExtend;
use common\models\User;
use frontend\actions\ApiAction;
use WechatPay\GuzzleMiddleware\Util\AesUtil;
use Yii;

class NotifyApi extends ApiAction
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
//            $this->valToken();

            $this->_get = Yii::$app->request->get();

//            $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
//
//            if (empty($this->_userId)) {
//                return $this->fail('请您给出用户信息', ErrorCode::USER_NOT_FOUND);
//            }
//
//            $this->_userInfo = User::findOne($this->_userId);

            switch ($this->action) {
                case 'notify':
                    $ret = $this->notify();
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
     * 微信支付回调
     * @return array
     * @throws \yii\db\Exception
     */
    public function notify() {
        $data = file_get_contents('php://input');

        $arrInput = json_decode($data, true);
        file_put_contents('/tmp/wechatpay.log', 'arr post : ' . json_encode($arrInput, true) . "\n", FILE_APPEND);

        $mchkey = md5('choice851111');
        $mchkey = 'b12c28d4741e950b30ff94ee44b7843c';

        $arrPost = $arrInput['resource'];

        // 解密入参
        $cipher = (new AesUtil($mchkey))->decryptToString($arrPost['associated_data'], $arrPost['nonce'], $arrPost['ciphertext']);
        $arrData = json_decode($cipher, true);

        file_put_contents('/tmp/wechatpay.log', 'arr data : ' . json_encode($arrData, true) . "\n", FILE_APPEND);

        if (!empty($arrData['out_trade_no'])
            && isset($arrData['trade_state'])
            && $arrData['trade_state'] == 'SUCCESS'
        ) {
            $order = Order::findOne(['order_no' => $arrData['out_trade_no']]);
            file_put_contents('/tmp/wechatpay.log', "order : " . json_encode($order, true) . "\n", FILE_APPEND);
            if ($order) {
                $order->transaction_id = !empty($arrData['transaction_id']) ? $arrData['transaction_id'] : '';
                $order->order_status = Order::ORDER_STATUS_COMPLETED;
                $order->save();
                file_put_contents('/tmp/wechatpay.log', 'order save succ!', FILE_APPEND);
            }
        }


        return [
            'code' => '成功',
            'message' => '成功',
        ];
//    }

//        $transaction = Yii::$app->db->beginTransaction();
//
//        if (empty($this->_get['order_id'])) {
//            throw new Exception('请您给出订单信息', ErrorCode::ORDER_NOT_FOUND);
//        }
//
//        $orderId = $this->_get['order_id'];
//
//        $order = Order::findOne($orderId);
//
//        if (empty($order)) {
//            throw new Exception('订单不存在', ErrorCode::ORDER_NOT_FOUND);
//        }
//
//        if ($order->order_status != Order::ORDER_STATUS_WAIT) {
//            throw new Exception('订单状态不正确', ErrorCode::ORDER_STATUS_ERROR);
//        }
//
//        if (empty($order->story)) {
//            throw new Exception('剧本不存在', ErrorCode::STORY_NOT_FOUND);
//        }
//        $story = $order->story;
//
//        try {
//
//            $res = Yii::$app->wechatPay->createH5Order($story, $order, $this->_userInfo);
//
//            $order->order_status = Order::ORDER_STATUS_PAYING;
////            $ret = $order->save();
//
//            $ret = [
//                'pay_res' => $res,
//                'order' => $order,
//            ];
//
//            $transaction->commit();
//        } catch (\Exception $e) {
////            $order->order_status = Order::ORDER_STATUS_PAY_FAILED;
//            $transaction->rollBack();
//            throw $e;
//        }
//
//        return $ret;
    }



}