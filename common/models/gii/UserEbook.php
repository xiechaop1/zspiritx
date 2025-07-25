<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_model}}".
 *
 */
class UserEbook extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_ebook}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'user_id', 'ebook_story', 'ebook_status', ], 'integer'],
            [['created_at', 'updated_at',], 'integer'],
            [['ebook_story_params', ], 'string'],
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
