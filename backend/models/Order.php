<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/3/25
 * Time: 上午11:26
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class Order extends \common\models\Order
{
    public $mobile;


    public $date_range;

    public function rules()
    {
        return [
            [['user_id', 'story_id', 'pay_method', 'item_type', 'item_id', 'order_status', 'status', 'expire_time', 'created_at', 'updated_at'], 'integer'],
            [['story_price', 'amount', 'refund_amount'], 'number'],
            [['order_no', 'transaction_id', 'refund_no', 'ver_code', 'ver_platform', ], 'string'],
//            [['attach', ], 'string'],
        ];
    }

    public function search($params)
    {
        $query = \common\models\Order::find();
        $query->with('user');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort' => false
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ],
        ]);

        if (isset($params['Order']['order_status']) && $params['Order']['order_status'] >= 0) {
            $query->andFilterWhere([
                'o_order.order_status'  => $params['Order']['order_status']
            ]);
        } else {
//            $query->andFilterWhere([
//                'o_order.order_status'  => [
//                    \common\models\Order::ORDER_STATUS_WAIT,
//                    \common\models\Order::ORDER_STATUS_PAIED,
//                    \common\models\Order::ORDER_STATUS_COMPLETED
//                ]
//            ]);
        }

//        if (!($this->load($params) && $this->validate())) {
//            return $dataProvider;
//        }

        if (!empty($params['Order']['mobile'])) {
            $query->joinWith(['user' => function($model) use ($params) {
                return $model->andFilterWhere(['like', 'o_user.mobile', $params['Order']['mobile']]);
            }]);
        }
//        $query->andFilterWhere([
//            'like', 'mobile', $this->mobile
//        ]);
        if (!empty($params['Order']['user_id'])) {
            $query->andFilterWhere([
                'o_order.user_id' => $params['Order']['user_id']
            ]);
        }

        if (!empty($params['Order']['item_type'])) {
            $query->andFilterWhere([
                'o_order.item_type' => $params['Order']['item_type']
            ]);
        }

        if (empty($params['is_delete'])) {
            $query->andFilterWhere([
                'o_order.is_delete' => \common\definitions\Common::STATUS_NORMAL
            ]);
        } else {
            $query->andFilterWhere([
                'o_order.is_delete' => $params['is_delete']
            ]);
        }

        if (!empty($params['Order']['date_range'])) {
            $query->andFilterWhere([
                '>', 'created_at', time() - $params['Order']['date_range'] * 24 * 3600
            ]);
        }

        $query->orderBy(['created_at' => SORT_DESC]);
//var_dump($query->createCommand()->getRawSql());exit;
        return $dataProvider;


    }

}