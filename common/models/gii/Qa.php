<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%qa}}".
 *
 * @property int $id
 * @property string $topic
 * @property string $attachment
 * @property string $st_answer
 * @property string $selected
 * @property int $qa_type
 * @property int $created_at
 * @property int $updated_at
 */
class Qa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%qa}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qa_type', 'story_id', 'knowledge_id', 'story_stage_id', 'created_at', 'updated_at'], 'integer'],
            [['topic', 'voice', 'attachment', 'st_answer', 'st_selected', 'selected'], 'string'],
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
