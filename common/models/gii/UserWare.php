<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user_score}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $story_id
 * @property int $session_id
 * @property int $team_id
 * @property int $score
 * @property int $created_at
 * @property int $updated_at
 */
class UserWare extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_ware}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'story_id', 'ware_id', 'ware_type', 'link_id', 'link_type',
                'user_ware_status', 'is_delete', 'status',
                'expire_time', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id'   => 'User ID',
            'story_id'  => 'Story ID',
            'session_id'    => 'Session ID',
            'team_id'   => 'Team ID',
            'score' => 'Score',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
