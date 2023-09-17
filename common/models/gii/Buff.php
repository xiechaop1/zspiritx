<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%qa}}".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 */
class Buff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%buff}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['buff_type', 'expire_time', 'created_at', 'updated_at'], 'integer'],
            [['buff_name', 'buff_desc', ], 'string'],
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
