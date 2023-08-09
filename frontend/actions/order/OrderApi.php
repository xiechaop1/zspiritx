<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\order;


use common\models\Log;
use common\models\Order;
use common\models\User;
use common\models\UserList;
use common\models\UserMusicList;
use common\models\Music;
use frontend\actions\ApiAction;
use Yii;

class OrderApi extends ApiAction
{
    public $action;
    private $_get;
    private $_musicId;
    private $_userId;

    private $_musicInfo;

    private $_userInfo;

    public function run()
    {

        try {
            $this->valToken();

            $this->_get = Yii::$app->request->get();

            $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

            if (empty($this->_userId)) {
                return $this->fail('请您给出用户信息', -199);
            }

            if (empty($this->_get['music_id'])) {
                return $this->fail('请您给出歌曲信息', -100);
            } else {
                $this->_musicId = $this->_get['music_id'];

                // 检查音乐是否存在
                $this->_musicInfo = Music::findOne($this->_musicId);
                if (empty($this->_musicInfo)) {
                    return $this->fail('歌曲不存在', -101);
                }
            }

            $this->_userInfo = User::findOne($this->_userId);

            switch ($this->action) {
                case 'lock':
                    $ret = $this->lock();
                    break;
                case 'unlock':
                    $ret = $this->unlock();
                    break;
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

        return $ret;
    }


    public function lock() {

        $model = new Order();
        $transaction = Yii::$app->db->beginTransaction();

        try {

            // 检查音乐是否被锁定
            $lockMusicInfo = Order::find()
                ->where([
                    'music_id'  => $this->_musicId,
                ])
//                ->andFilterWhere(
//                    ['<>', 'user_id', $this->_userId]
//                )
                ->andFilterWhere([
                    'order_status' => [Order::ORDER_STATUS_LOCK],
                ])
                ->andFilterWhere([
//                    'or',
//                    ['=' ,'expire_time', 0],
                    '>', 'expire_time', time(),
                ])
//                ->createCommand()
//                ->getRawSql();
//            var_dump($lockMusicInfo);exit;
                ->one();

            if (!empty($lockMusicInfo)) {
                return $this->fail('当前音乐已经被锁定', -200);
            }

            // 检查音乐是否被锁定
            $boughtMusicInfo = Order::find()
                ->where([
                    'music_id'  => $this->_musicId,
                ])
//                ->andFilterWhere(
//                    ['<>', 'user_id', $this->_userId]
//                )
                ->andFilterWhere([
                    'order_status' => [Order::ORDER_STATUS_COMPLETED, Order::ORDER_STATUS_PAIED],
                ])
                ->one();

            if (!empty($boughtMusicInfo)) {
                return $this->fail('当前音乐已经被购买', -201);
            }

            // 检查当前用户锁定音乐数量
            $lockMusicCount = Order::find()
                ->where([
                    'user_id' => $this->_userId,
                    'order_status' => Order::ORDER_STATUS_LOCK,
                ])
                ->orFilterWhere([
                    'and',
                    'expire_time' => 0,
                    ['>', 'expire_time', time()],
                ])
                ->count();

            if ($lockMusicCount >= $this->_userInfo['max_lock_ct']) {
                return $this->fail('当前用户锁定音乐数量已经达到上限', -201);
            }

            $orderInfo = Order::findOne([
                'user_id' => $this->_userId,
                'music_id' => $this->_musicId,
                'order_status' => Order::ORDER_STATUS_LOCK,
            ]);

            $lockDays = !empty(Yii::$app->params['lockDays']) ? Yii::$app->params['lockDays'] : 30;
            if (!empty($orderInfo)) {
                $orderInfo->updated_at = time();
                $orderInfo->expire_time = $orderInfo->created_at + $lockDays * 86400;
                $orderInfo->save();
            } else {

                $model->user_id = $this->_userId;
                $model->music_id = $this->_musicId;
                $model->order_status = Order::ORDER_STATUS_LOCK;
                $model->order_permission = Order::ORDER_PERMISSION_DOWNLOAD_YES;
                $model->expire_time = time() + $lockDays * 86400;
                $model->save();
            }

            $this->_musicInfo->music_status = Music::MUSIC_STATUS_LOCK;
            $this->_musicInfo->op_user_id = $this->_userId;
            $this->_musicInfo->save();

            Yii::$app->oplog->write(\common\models\Log::OP_CODE_LOCK, Log::OP_STATUS_SUCCESS, $this->_userId, $this->_musicId, '用户锁定');

            $transaction->commit();
            return $this->success('操作成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->oplog->write(\common\models\Log::OP_CODE_LOCK, Log::OP_STATUS_FAILED, $this->_userId, $this->_musicId, '用户锁定');

            return $this->fail('操作失败', -1000);
        }

    }

    public function unlock() {

        $model = new Order();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // 检查音乐是否被锁定
            $lockMusicInfo = Order::find()
                ->andFilterWhere([
                    'music_id'  => $this->_musicId,
                    'order_status' => Order::ORDER_STATUS_LOCK,
                ])
                ->andFilterWhere(
                    ['=', 'user_id', $this->_userId]
                )
                ->orFilterWhere([
                    'and',
                    'expire_time' => 0,
                    ['>', 'expire_time', time()],
                ])
                ->one();

            if (empty($lockMusicInfo)) {
                return $this->fail('当前音乐没有被当前用户锁定', -205);
            }

            $lockMusicInfo->expire_time = 0;
            $lockMusicInfo->order_status = Order::ORDER_STATUS_CANCELED;
            $lockMusicInfo->save();

            $this->_musicInfo->music_status = Music::MUSIC_STATUS_NORMAL;
            $this->_musicInfo->op_user_id = 0;
            $this->_musicInfo->save();

            Yii::$app->oplog->write(\common\models\Log::OP_CODE_UNLOCK, Log::OP_STATUS_SUCCESS, $this->_userId, $this->_musicId, '用户解锁');

            $transaction->commit();
            return $this->success('操作成功');
        } catch (\Exception $e) {
            $transaction->rollBack();

            Yii::$app->oplog->write(\common\models\Log::OP_CODE_UNLOCK, Log::OP_STATUS_FAILED, $this->_userId, $this->_musicId, '用户解锁');

            return $this->fail('操作失败', -1000);
        }

    }

    public function create() {
        $transaction = Yii::$app->db->beginTransaction();

        $listInfo = UserList::findOne([
            'user_id' => $this->_userId,
            'list_type' => UserList::LIST_TYPE_LOCK,
        ]);

        $listId = $listInfo['id'];

        // 查找锁定音乐
        $lockMusicInfo = Order::find()
            ->andFilterWhere([
                'music_id'  => $this->_musicId,
                'order_status' => Order::ORDER_STATUS_LOCK,
            ])
            ->andFilterWhere(
                ['=', 'user_id', $this->_userId]
            )
            ->orFilterWhere([
                'and',
                'expire_time' => 0,
                ['>', 'expire_time', time()],
            ])
            ->one();

        if (empty($lockMusicInfo)) {
            return $this->fail('当前音乐尚未被您锁定', -205);
        }

        $payMethod = 1;     // 默认微信支付方式

        // 如果售价为0，就是已支付，如果售价>0，就是待支付
        if ($this->_musicInfo['price'] > 0) {
            $orderStatus = Order::ORDER_STATUS_WAIT;
            $expireTime = time() + 1800;    // 30分钟支付
        } else {
            $orderStatus = Order::ORDER_STATUS_PAIED;
            $expireTime = 0;
        }

        try {
//            $orderModel = new Order();
//            $orderModel->user_id = $this->_userId;
//            $orderModel->music_id = $this->_musicId;
            $lockMusicInfo->price = $this->_musicInfo['price'];
            $lockMusicInfo->amount = $this->_musicInfo['price'];
            $lockMusicInfo->pay_method = $payMethod;
            $lockMusicInfo->order_status   = $orderStatus;
            $lockMusicInfo->expire_time = $expireTime;
            $lockMusicInfo->save();

            $this->_musicInfo->music_status = Music::MUSIC_STATUS_BOUGHT;
            $this->_musicInfo->op_user_id = $this->_userId;
            $this->_musicInfo->save();

            Yii::$app->oplog->write(\common\models\Log::OP_CODE_ORDER, 1, $this->_userId, $this->_musicId, $this->_userId . '下单歌曲' . $this->_musicId . '状态：' . $orderStatus);
            if ($orderStatus == Order::ORDER_STATUS_PAIED) {
                Yii::$app->oplog->write(\common\models\Log::OP_CODE_COMPLETED, 1, $this->_userId, $this->_musicId, $this->_userId . '完成购买歌曲' . $this->_musicId . '状态：' . $orderStatus);
            }

            $transaction->commit();
            return $this->success('操作成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->oplog->write(\common\models\Log::OP_CODE_ORDER, 0, $this->_userId, $this->_musicId, $this->_userId . '下单歌曲' . $this->_musicId);
            if ($orderStatus == Order::ORDER_STATUS_PAIED) {
                Yii::$app->oplog->write(\common\models\Log::OP_CODE_COMPLETED, 0, $this->_userId, $this->_musicId, $this->_userId . '完成购买歌曲' . $this->_musicId . '状态：' . $orderStatus);
            }
            return $this->fail('操作失败', -1000);
        }
    }



}