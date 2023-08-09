<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\order;


use common\models\Order;
use common\models\User;
use common\models\UserList;
use common\models\UserMusicList;
use common\models\Music;
use frontend\actions\ApiAction;
use Yii;

class GetOrderApi extends ApiAction
{
    public $action;
    private $_get;
    private $_musicId;
    private $_userId;

    private $_musicInfo;

    private $_userInfo;

    public function run()
    {

        $this->_get = Yii::$app->request->get();

        $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

        $this->_userInfo = User::findOne($this->_userId);

        switch ($this->action) {
            case 'order':
                $ret = $this->order();
                break;
            default:
                $ret = [];
                break;

        }

        return $ret;
    }

    public function order() {
        $orderId = $this->_get['id'];
        $userId = !empty($this->_userId) ? $this->_userId : 0;
        $order = Order::find()->where(['id' => $orderId])->with('musicwithoutstatus')->asArray()->one();

        if (empty($order)) {
            return $this->fail('订单不存在', -100);
        } else {
            if ($order['user_id'] != $userId) {
                return $this->fail('订单不属于当前用户', -101);
            } else {
                $order['musicwithoutstatus'] = \common\helpers\Music::formatSource($order['musicwithoutstatus']);
                $order['created_at_friendly'] = Date('Y.m.d', $order['created_at']);
                $order['updated_at_friendly'] = Date('Y.m.d', $order['updated_at']);
                $order['expire_time_friendly'] = Date('Y.m.d', $order['expire_time']);
                return $this->success($order);
            }
        }
    }



}