<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Knowledge extends \common\models\gii\Knowledge
{
    const KNOWLEDGE_CLASS_NORMAL    = 1;    // 知识
    const KNOWLEDGE_CLASS_MISSSION  = 2;    // 任务

    const KNOWLEDGE_TYPE_TEXT = 1;      // 文本
    const KNOWLEDGE_TYPE_IMAGE = 2;     // 图片
    const KNOWLEDGE_TYPE_AUDIO = 3;     // 音频
    const KNOWLEDGE_TYPE_VIDEO = 4;     // 视频

    public static $knowledgeClass2Name = [
        self::KNOWLEDGE_CLASS_NORMAL => '知识',
        self::KNOWLEDGE_CLASS_MISSSION => '任务',
    ];

    public static $knowledgeType2Name = [
        self::KNOWLEDGE_TYPE_TEXT => '文本',
        self::KNOWLEDGE_TYPE_IMAGE => '图片',
        self::KNOWLEDGE_TYPE_AUDIO => '音频',
        self::KNOWLEDGE_TYPE_VIDEO => '视频',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

    public function getStory(){
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }


}