<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user_friends}}".
 *
 * @property int $created_at
 * @property int $updated_at
 */
class UserFriend extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_friends}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'to_user_id', 'user_friend_status', 'created_at', 'updated_at'], 'integer'],
            [['invite_word', ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'User Name',
            'wx_id'  => 'WX ID',
            'mobile'    => 'Mobile',
            'avatar'    => 'Avatar',
            'user_status'   => 'User Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
