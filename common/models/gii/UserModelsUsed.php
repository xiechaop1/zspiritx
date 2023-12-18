<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_model}}".
 *
 */
class UserModelsUsed extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_model_used}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'story_model_link_id', 'user_id', 'session_id', 'user_model_id', 'story_model_id', 'story_model_detail_id',
                'story_model_id2', 'story_model_detail_id2', 'eff_type'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['eff_exec', 'group_name' ], 'string'],
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
