<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_match}}".
 *
 */
class StoryMatch extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story_match}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'session_id', 'user_id', 'match_type', 'm_story_model_id', 'm_story_model_detail_id',
//                'team_id', 'poi_id',        // Todo：这里的字段需要根据实际情况修改
                'user_model_id', 'score', 'score2',], 'integer'],
            [['created_at', 'updated_at', 'status', 'story_match_status', ], 'integer'],
            [['match_name', 'match_detail', 'ret',
//                'match_id',         // Todo：这里的字段需要根据实际情况修改
                ], 'string'],
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
