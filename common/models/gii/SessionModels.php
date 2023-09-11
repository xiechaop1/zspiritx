<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%session_model}}".
 *
 * @property int $id
 * @property int $story_id
 * @property int $building_id
 * @property int $poi_id
 * @property int $session_id
 * @property int $timebegin
 * @property int $timeend
 * @property int $rate
 * @property int $scan_type
 * @property int $pre_story_model_id
 * @property int $model_id
 * @property int $misrange
 * @property int $sort_by
 * @property int $status
 * @property int $is_delete
 * @property int $created_at
 * @property int $updated_at
 * @property string $scan_image_id
 * @property string $snapshot
 * @property double $lat
 * @property double $lng
 * @property double $show_x
 * @property double $show_y
 * @property double $show_z
 * @property int $is_unique
 * @property int $is_pickup
 */
class SessionModels extends \yii\db\ActiveRecord
{

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
            [['lat', 'lng', 'show_x', 'show_y', 'show_z' ], 'number'],
            [['building_id', 'poi_id', 'session_id', 'timebegin', 'timeend', 'rate',
                'scan_type', 'pre_story_model_id', 'model_id', 'misrange', 'sort_by', 'is_unique', 'is_pickup',
                'last_operator_id', 'session_model_status', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
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
