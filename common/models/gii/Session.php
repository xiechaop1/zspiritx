<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%session}}".
 *
 * @property int $id
 * @property int $session_status 场次状态
 * @property string $session_name 场次名称
 * @property int $created_at
 * @property int $updated_at
 */
class Session extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%session}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'story_id', 'session_status', 'created_at', 'updated_at'], 'integer'],
            [['session_name', 'password_code', 'user_agent', ], 'string'],
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
