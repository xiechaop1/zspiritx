<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%music}}".
 *
 */
class Story extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'desc', 'guide', 'story_bg', 'thumbnail', 'cover_image', 'image', 'latest_unity_version', ], 'string'],
            [['persons_ct', 'roles_ct', 'story_type', 'story_status', 'status', 'sort_by'], 'integer'],
            [['resources'], 'string'],
            [['is_debug', 'is_delete', 'created_at', 'updated_at',], 'integer'],
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
