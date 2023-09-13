<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%session_model}}".
 *
 * @property int $id
 * @property int $story_id
 * @property int $building_id
 * @property int $session_id
 * @property int $story_stage_id
 * @property int $stage_status
 */
class SessionStages extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%session_stage}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['session_id', 'story_stage_id', 'story_id', 'stage_status', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['snapshot'], 'string'],
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
