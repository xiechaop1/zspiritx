<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_stage}}".
 *
 */
class StoryStages extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story_stage}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lat', 'lng', 'show_x', 'show_y', 'show_z' ], 'number'],
            [['story_id', 'timebegin', 'timeend', 'rate', 'scan_type', 'pre_stage_id', 'misrange', 'sort_by', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['scan_image_id', 'stage_u_id', 'stage_name'], 'string'],
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
