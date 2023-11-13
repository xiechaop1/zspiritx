<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "{{%user_story}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property int $story_id
 * @property int $session_id
 * @property int $team_id
 * @property int $building_id
 * @property int $buff
 * @property int $debuff
 * @property int $buff_expiretime
 * @property int $debuff_expiretime
 * @property string $goal
 * @property string $goal_right
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserStory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_story}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'role_id', 'story_id',
                'session_id', 'team_id', 'building_id',
                'last_story_stage_id', 'last_session_story_id',
                'buff', 'debuff', 'buff_expiretime', 'debuff_expiretime',
                'status', 'created_at', 'updated_at'], 'integer'],
            [['goal', 'goal_correct'], 'string'],
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
