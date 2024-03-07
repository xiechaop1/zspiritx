<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user_lottery}}".
 *
 */
class UserLottery extends \yii\db\ActiveRecord
{

    public $lyricJson;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_lottery}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'user_id', 'lottery_id', 'expire_time', 'ct',
                'lottery_id', 'lottery_status', 'story_id', 'session_id', 'channel_id', 'status'], 'integer'],
            [[ 'created_at', 'updated_at',], 'integer'],
            [['lottery_no'], 'string'],
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
