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
            [['is_unique', 'is_visable', 'is_placing_hint', 'use_allow', 'selected_permission', 'namecard_display',
                'is_undertake', 'undertake_alive_timeout', 'undertake_trigger_timeout',
                'story_stage_id', 'story_id', 'story_model_detail_id', 'story_model_class', 'pos_story_model_id',
                'building_id', 'poi_id', 'timebegin', 'timeend', 'is_random',
                'rate', 'scan_type', 'set_type', 'pre_story_model_id', 'model_id',
                 'active_type', 'direction', 'sort_by', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['active_model_inst_u_id', 'target_model_u_id', 'scan_image_id', 'model_inst_u_id',
                'active_next', 'story_model_name', 'story_model_desc', 'dialog', 'dialog2', 'story_model_prop',
                'model_group', 'use_group_name', 'story_model_image', 'icon', 'story_model_html',
//                'story_model_config',
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
