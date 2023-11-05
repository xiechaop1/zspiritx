<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_model}}".
 *
 */
class StoryModels extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story_model}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lat', 'lng', 'show_x', 'show_y', 'show_z', 'misrange', 'trigger_misrange', 'act_misrange', 'scale' ], 'number'],
            [['is_unique', 'is_visable', 'use_allow',
                'is_undertake', 'undertake_alive_timeout', 'undertake_trigger_timeout',
                'story_stage_id', 'story_id', 'story_model_detail_id',
                'building_id', 'poi_id', 'timebegin', 'timeend',
                'rate', 'scan_type', 'pre_story_model_id', 'model_id',
                 'active_type', 'direction', 'sort_by', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['scan_image_id', 'model_inst_u_id', 'active_next', 'story_model_name', 'story_model_desc', 'dialog', 'model_group', ], 'string'],
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
