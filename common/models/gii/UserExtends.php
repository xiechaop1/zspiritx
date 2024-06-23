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
class UserExtends extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_extends}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grade','level', 'user_id', 'story_id', 'created_at', 'updated_at'], 'integer'],
            [['home_lng', 'home_lat', ], 'number'],
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
