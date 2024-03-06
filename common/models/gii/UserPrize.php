<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%lottery_log}}".
 *
 */
class UserPrize extends \yii\db\ActiveRecord
{

    public $lyricJson;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_prize}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'user_id', 'prize_id', 'prize_type', 'award_method', 'prize_type', 'expire_time',
                'lottery_id', 'user_prize_status', 'story_id', 'session_id', 'channel_id', 'status'], 'integer'],
            [['extend_info',] , 'string'],
            [[ 'created_at', 'updated_at',], 'integer'],
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
