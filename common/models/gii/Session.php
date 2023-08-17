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
            [['session_name'], 'string'],
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
