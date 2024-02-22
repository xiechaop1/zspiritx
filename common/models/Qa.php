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
    const QA_TYPE_SINGLE = 1;     // 单选题
    const QA_TYPE_PIC = 2;      // 图片题
    const QA_TYPE_VIDEO = 3;    // 视频题
    const QA_TYPE_MULTI = 4;    // 多选题

    const QA_TYPE_PUZZLE_WORD = 5;     // 拼图文字题
    const QA_TYPE_PUZZLE_PIC = 6;      // 拼图图片题
    const QA_TYPE_WORD = 7;      // 填空题
    const QA_TYPE_VERIFYCODE = 8;      // 验证码题
    const QA_TYPE_CHATGPT = 9;      // 闲聊题
    const QA_TYPE_SECRET = 10;      // 密码锁

    const QA_TYPE_SUDOKU = 11;      // 数独题

    const QA_TYPE_SELECTION = 12;      // 选项

    const QA_TYPE_PHONE = 13;      // 手机

    public static $qaType2Name = [
        self::QA_TYPE_SINGLE  => '单选题',
        self::QA_TYPE_PIC   => '图片题',
        self::QA_TYPE_VIDEO => '视频题',
        self::QA_TYPE_MULTI => '多选题',
        self::QA_TYPE_PUZZLE_WORD  => '拼图文字题',
        self::QA_TYPE_PUZZLE_PIC   => '拼图图片题',
        self::QA_TYPE_WORD => '填空题',
        self::QA_TYPE_VERIFYCODE => '验证码题',
        self::QA_TYPE_CHATGPT => '闲聊题',
        self::QA_TYPE_SECRET => '密码锁',
        self::QA_TYPE_SUDOKU => '数独题',
        self::QA_TYPE_SELECTION => '选项',
        self::QA_TYPE_PHONE => '手机',
    ];

    public static $qaTypeIsJson = [
        self::QA_TYPE_PUZZLE_WORD,
        self::QA_TYPE_PUZZLE_PIC,
        self::QA_TYPE_VERIFYCODE,
        self::QA_TYPE_SELECTION,
        self::QA_TYPE_PHONE,
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