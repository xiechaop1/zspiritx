<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user_book}}".
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
class UserBook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_book}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'session_id', 'story_id','poi', 'page', 'created_at', 'updated_at'], 'integer'],
            [['pic_url', 'video_url', 'intro'], 'string'],
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
