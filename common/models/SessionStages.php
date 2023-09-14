<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class SessionStages extends \common\models\gii\SessionStages
{
    const SESSION_STAGE_STATUS_READY        = 0;    // 尚未被放置
    const SESSION_STAGE_STATUS_SET          = 1;    // 被放置
    const SESSION_STAGE_STATUS_OPERATING    = 2;    // 被操作

    public static $sessionStageStatus2Name = [
        self::SESSION_STAGE_STATUS_READY      => '未放置',
        self::SESSION_STAGE_STATUS_SET        => '已放置',
        self::SESSION_STAGE_STATUS_OPERATING  => '被操作',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getStage(){
        return $this->hasOne('common\models\StoryStages',  ['id' => 'story_stage_id']);
    }

    public function getModels() {
        return $this->hasMany('common\models\SessionModels', ['story_stage_id' => 'story_stage_id', 'session_id' => 'session_id']);
    }


}