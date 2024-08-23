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
    const QA_TYPE_PHONE_SMS = 14;      // 手机短信
    const QA_TYPE_AR = 21;      // AR

    const QA_TYPE_GPT_SUBJECT = 30; // GPT出题

    const QA_CLASS_NORMAL   = 1; // 普通题
//    const QA_CLASS_POEM     = 2; // 诗词题
    const QA_CLASS_MATH          = 20; // 数学
    const QA_CLASS_ENGLISH       = 30; // 英语
    const QA_CLASS_CHINESE       = 40; // 语文
    const QA_CLASS_POEM          = 41; // 诗词
    const QA_CLASS_POEM_IDIOM    = 42; // 成语
    const QA_CLASS_HISTORY       = 50; // 历史
    const QA_CLASS_PHYSICS       = 60; // 物理
    const QA_CLASS_ANY           = 999;    // 通用

    public static $qaClass2Name = [
//        self::QA_CLASS_NORMAL => '普通题',
//        self::QA_CLASS_RANDOM => '随机题',
        self::QA_CLASS_NORMAL => '普通题',
        self::QA_CLASS_MATH => '数学题',
        self::QA_CLASS_ENGLISH  => '英语题',
        self::QA_CLASS_CHINESE  => '语文题',
        self::QA_CLASS_POEM => '诗词题',
        self::QA_CLASS_POEM_IDIOM   => '成语题',
        self::QA_CLASS_HISTORY  => '历史题',
        self::QA_CLASS_PHYSICS => '物理题',
        self::QA_CLASS_ANY => '通用题',
    ];

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
        self::QA_TYPE_PHONE_SMS => '手机短信',

        self::QA_TYPE_AR => 'AR',
        self::QA_TYPE_GPT_SUBJECT => 'GPT出题',

    ];

    const QA_MODE_NORMAL = 1; // 普通模式
    const QA_MODE_RANDOM = 2; // 随机模式
    const QA_MODE_MATCH  = 3; // 竞赛模式

    public static $qaMode2Name = [
        self::QA_MODE_NORMAL => '普通模式',
        self::QA_MODE_RANDOM => '随机模式',
        self::QA_MODE_MATCH => '竞赛模式',
    ];

    public static $qaTypeIsJson = [
        self::QA_TYPE_PUZZLE_WORD,
        self::QA_TYPE_PUZZLE_PIC,
        self::QA_TYPE_WORD,
        self::QA_TYPE_VERIFYCODE,
        self::QA_TYPE_SELECTION,
        self::QA_TYPE_PHONE,
        self::QA_TYPE_PHONE_SMS,
        self::QA_TYPE_SUDOKU,
        self::QA_TYPE_SECRET,
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

    public function getUserQas() {
        return $this->hasMany('common\models\UserQa', ['qa_id' => 'id']);
    }

}