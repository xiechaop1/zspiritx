<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_stage_link}}".
 *
 */
class StoryStageLink extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story_stage_link}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_stage_id', 'pre_story_stage_id', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
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
