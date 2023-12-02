<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Qa extends \common\models\gii\Qa
{
    const QA_TYPE_WORD = 1;     // 文字题
    const QA_TYPE_PIC = 2;      // 图片题
    const QA_TYPE_VIDEO = 3;    // 视频题
    const QA_TYPE_MULTI = 4;    // 多选题

    const QA_TYPE_PUZZLE_WORD = 5;     // 拼图文字题
    const QA_TYPE_PUZZLE_PIC = 6;      // 拼图图片题

    public static $qaType2Name = [
        self::QA_TYPE_WORD  => '文字题',
        self::QA_TYPE_PIC   => '图片题',
        self::QA_TYPE_VIDEO => '视频题',
        self::QA_TYPE_MULTI => '多选题',
        self::QA_TYPE_PUZZLE_WORD  => '拼图文字题',
        self::QA_TYPE_PUZZLE_PIC   => '拼图图片题',
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
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getKnowledge(){
        return $this->hasOne('common\models\Knowledge',  ['id' => 'knowledge_id']);
    }

}