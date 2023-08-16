<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%story_role}}".
 *
 * @property int $id
 * @property int $story_id
 * @property string $role_name
 * @property string $role_desc
 * @property int $role_max_ct
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
            [['story_id', 'role_max_ct', 'created_at', 'updated_at'], 'integer'],
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
