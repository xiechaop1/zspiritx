<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_role}}".
 *
 * @property int $id
 * @property int $tag_type 0非特殊
 * @property string $tag_name 标签名
 * @property int $created_at
 * @property int $updated_at
 */
class StoryRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story_role}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'created_at', 'updated_at'], 'integer'],
            [['role_name'], 'string', 'max' => 32],
            [['role_desc'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_type' => 'Tag Type',
            'tag_name' => 'Tag Name',
            'level'     => 'Leve',
            'parent_id' => 'Parent Id',
            'deleted_at'    => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
