<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $created_at
 * @property int $updated_at
 * @property int $id
 * @property string $user_name
 * @property string $user_pass
 * @property string $nick_name
 * @property string $wx_openid
 * @property string $wx_unionid
 * @property string $wx_token
 * @property int $wx_token_expire_time
 * @property string $mobile
 * @property string $avatar
 * @property int $user_status
 * @property int $last_login_time
 * @property double $last_login_geo_lat
 * @property double $last_login_geo_lng
 * @property int $status
 * @property int $is_delete
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_status', 'status', 'last_login_time', 'wx_token_expire_time', 'created_at', 'updated_at'], 'integer'],
            [['user_name', 'user_pass', 'nick_name', 'user_pass', 'wx_openid', 'wx_unionid', 'wx_token', 'mobile', 'avatar', ], 'string'],
            [['last_login_geo_lat', 'last_login_geo_lng'], 'number'],
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
            'user_pass' => 'User Pass',
            'mobile'    => 'Mobile',
            'avatar'    => 'Avatar',
            'user_status'   => 'User Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
