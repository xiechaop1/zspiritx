<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user_model}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $story_model_id
 * @property int $story_model_detail_id
 * @property int $session_model_id
 * @property int $session_id
 * @property int $model_id
 * @property int $use_ct
 * @property int $is_delete
 * @property int $created_at
 * @property int $updated_at
 */
class UserModels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_model}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'story_model_id', 'story_model_detail_id', 'session_model_id',
                'session_id', 'model_id', 'use_ct', 'is_delete',
                'status', 'created_at', 'updated_at'], 'integer'],
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
