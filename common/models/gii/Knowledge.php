<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%knowledge}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $story_id
 * @property int $qa_id
 * @property string $title
 * @property string $content
 * @property string $voice
 * @property string $linkurl
 * @property int $sort_by
 * @property int $created_at
 * @property int $updated_at
 */
class Knowledge extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%knowledge}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'story_id', 'sort_by', 'is_delete', 'story_stage_id', 'knowledge_class',
                'knowledge_type', 'pre_knowledge_id', 'rep_ct',
                'created_at', 'updated_at'], 'integer'],
            [['title', 'content', 'suggestion', 'voice', 'linkurl', 'image', 'comp_action' ], 'string'],
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
