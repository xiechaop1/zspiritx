<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_model}}".
 *
 */
class StoryModelDetail extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story_model_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_unique', 'pre_story_model_id', 'story_id',
                 'active_type', 'direction', 'sort_by', 'status'], 'integer'],
            [['active_expiretime', 'created_at', 'updated_at',], 'integer'],
            [['active_next', 'dialog', 'title' ], 'string'],
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
