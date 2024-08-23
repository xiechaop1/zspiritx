<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_model_special_eff}}".
 *
 */
class StoryModelSpecialEff extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story_model_special_eff}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'model_id', 'level', 'eff_class', 'during_ti', 'cd', 'eff_mode', 'link_story_model_id', 'own_story_model_id', 'status'], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['special_eff_name', 'special_eff_desc', 'icon', 'model_inst_u_id', 'prop', 'env_eff', ], 'string'],
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
