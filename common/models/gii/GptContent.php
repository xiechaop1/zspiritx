<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%gpt_content}}".
 *
 */
class GptContent extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%gpt_content}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gpt_model',  ], 'string'],
            [['user_id', 'sender_id', 'to_user_id', 'story_id', 'msg_type',], 'integer'],
            [['content', 'prompt'], 'string'],
            [['status', 'is_delete', 'created_at', 'updated_at',], 'integer'],
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
