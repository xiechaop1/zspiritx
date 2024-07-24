<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user_extends}}".
 *
 * @property int $created_at
 * @property int $updated_at
 * @property int $id
 */
class UserData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_data}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['story_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['data_date', 'data_type', 'data_value', 'time_type', ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
