<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%action}}".
 *
 * @property int $id
 * @property int $session_id 场次id
 * @property int $sender_id 发送者id
 * @property int $to_user 接收者id
 * @property int $action_type 动作类型
 * @property int $expire_time 过期时间
 * @property string $action_detail 动作详情
 * @property int $action_status 动作状态
 * @property int $is_delete
 * @property int $created_at
 * @property int $updated_at
 */
class Actions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%action}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_id', 'sender_id', 'to_user',
                'action_type', 'action_status', 'expire_time', 'session_stage_id',
                'is_delete', 'created_at', 'updated_at'], 'integer'],
            [['action_detail', ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $rules = $this->rules();
        $ret = [];
        foreach ($rules as $rule) {
            if (!empty($rule[0])) {
                foreach ($rule[0] as $r) {
                    $ret[$r] = preg_replace_callback('/[^|\s]+([a-z])/',function($matches){
//                        print_r($matches);  //Array ( [0] => _b [1] => b )
                        return strtoupper($matches[1]);
                    },$r);
                }
            }
        }

        return $ret;

    }
}
