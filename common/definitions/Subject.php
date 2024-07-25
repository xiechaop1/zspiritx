<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:13 PM
 */

namespace common\definitions;


class Subject
{
    const SUBJECT_CLASS_NORMAL        = 1; // 普通
    const SUBJECT_CLASS_MATH          = 20; // 数学
    const SUBJECT_CLASS_ENGLISH       = 30; // 英语
    const SUBJECT_CLASS_CHINESE       = 40; // 语文
    const SUBJECT_CLASS_POEM          = 41; // 诗词
    const SUBJECT_CLASS_POEM_IDIOM    = 42; // 成语
    const SUBJECT_CLASS_HISTORY       = 50; // 历史
    const SUBJECT_CLASS_PHYSICS       = 60; // 物理
    const SUBJECT_CLASS_ANY           = 999;    // 通用

    public static $subjectClass2Name = [
        self::SUBJECT_CLASS_NORMAL => '普通',
        self::SUBJECT_CLASS_MATH => '数学',
        self::SUBJECT_CLASS_ENGLISH => '英语',
        self::SUBJECT_CLASS_CHINESE => '语文',
        self::SUBJECT_CLASS_POEM => '诗词',
        self::SUBJECT_CLASS_POEM_IDIOM => '成语',
        self::SUBJECT_CLASS_HISTORY => '历史',
        self::SUBJECT_CLASS_PHYSICS => '物理',
        self::SUBJECT_CLASS_ANY => '通用',
    ];
}