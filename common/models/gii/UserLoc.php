<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $created_at
 * @property int $updated_at
 * @property int $id
 * @property int $user_id
 * @property double $lat
 * @property double $lng
 */
class UserLoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_loc}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['lat', 'lng'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'lat' => 'Lat',
            'lng'    => 'Lng',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
