<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%model}}".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 */
class Models extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%model}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_active', 'width', 'height', 'length',
                'model_type',
                'is_delete', 'created_at', 'updated_at'], 'integer'],
            [['model_name'], 'string', 'max' => 32],
            [['model_uri', 'model_desc', 'model_u_id'], 'string'],
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
    }
}
