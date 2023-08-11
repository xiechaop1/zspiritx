<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%session_model}}".
 *
 */
class SessionModels extends \yii\db\ActiveRecord
{

    public $lyricJson;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%session_model}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lat', 'lng', 'show_x', 'show_y', 'show_z', 'is_unique', 'is_pickup' ], 'number'],
            [['story_id', 'building_id', 'poi_id', 'session_id', 'timebegin', 'timeend', 'rate', 'scan_type', 'pre_story_model_id', 'model_id', 'misrange', 'sort_by', 'status'], 'integer'],
            [['is_delete', 'created_at', 'updated_at',], 'integer'],
            [['scan_image_id', 'snapshot'], 'string'],
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
