<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%lottery_log}}".
 *
 */
class LotteryPrize extends \yii\db\ActiveRecord
{

    public $lyricJson;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%lottery_prize}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prize_name', 'prize_level_name', 'prize_option', 'image', 'thumbnail' ], 'string'],
            [[
                'prize_method', 'total_ct', 'interval_ct', 'interval_type', 'rate', 'prize_type',
                'story_model_id', 'prize_level', 'lottery_id', 'prize_status', 'story_id', 'status'], 'integer'],
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
