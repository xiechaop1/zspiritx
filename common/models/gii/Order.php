<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $story_id 剧本ID
 * @property string $amount 成交价
 * @property string $story_price 原价
 * @property int $pay_method 支付方式 1微信
 * @property int $order_status 1待付款 2已付款 3已退款
 * @property string $attach 附件
 * @property string $contract 合同（暂时作废）
 * @property int $created_at
 * @property int $updated_at
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'story_id', 'pay_method', 'order_status', 'status', 'expire_time', 'created_at', 'updated_at'], 'integer'],
            [['story_price', 'amount'], 'number'],
//            [['attach', ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'story_id' => 'Story ID',
            'pay_method' => 'Pay Method',
            'story_price' => 'Story Price',
            'amount' => 'Amount',
            'order_status' => 'Order Status',
            'status'    => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
