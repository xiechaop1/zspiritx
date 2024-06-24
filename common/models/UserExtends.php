<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class UserExtends extends \common\models\gii\UserExtends
{
    const USER_SCHOOL_PRIMARY = 1; // 小学
    const USER_SCHOOL_JUNIOR = 2; // 初中
    const USER_SCHOOL_SENIOR = 3; // 高中
    const USER_SCHOOL_OTHER = 99; // 其他

    const USER_GRADE_PRIMARY_ONE = 1; // 小学一年级
    const USER_GRADE_PRIMARY_TWO = 2; // 小学二年级
    const USER_GRADE_PRIMARY_THREE = 3; // 小学三年级
    const USER_GRADE_PRIMARY_FOUR = 4; // 小学四年级
    const USER_GRADE_PRIMARY_FIVE = 5; // 小学五年级
    const USER_GRADE_PRIMARY_SIX = 6; // 小学六年级
    const USER_GRADE_PRIMARY_SEVEN = 7; // 小学七年级
    const USER_GRADE_PRIMARY_EIGHT = 8; // 小学八年级
    const USER_GRADE_PRIMARY_NINE = 9; // 小学九年级
    const USER_GRADE_JUNIOR_ONE = 11; // 初中一年级
    const USER_GRADE_JUNIOR_TWO = 12; // 初中二年级
    const USER_GRADE_JUNIOR_THREE = 13; // 初中三年级
    const USER_GRADE_SENIOR_ONE = 21; // 高中一年级
    const USER_GRADE_SENIOR_TWO = 22; // 高中二年级
    const USER_GRADE_SENIOR_THREE = 23; // 高中三年级
    const USER_GRADE_OTHER = 99; // 其他

    public static $userGrade2Name = [
        self::USER_GRADE_PRIMARY_ONE => '一年级',
        self::USER_GRADE_PRIMARY_TWO => '二年级',
        self::USER_GRADE_PRIMARY_THREE => '三年级',
        self::USER_GRADE_PRIMARY_FOUR => '四年级',
        self::USER_GRADE_PRIMARY_FIVE => '五年级',
        self::USER_GRADE_PRIMARY_SIX => '六年级',
        self::USER_GRADE_PRIMARY_SEVEN => '七年级',
        self::USER_GRADE_PRIMARY_EIGHT => '八年级',
        self::USER_GRADE_PRIMARY_NINE => '九年级',
        self::USER_GRADE_JUNIOR_ONE => '初一',
        self::USER_GRADE_JUNIOR_TWO => '初二',
        self::USER_GRADE_JUNIOR_THREE => '初三',
        self::USER_GRADE_SENIOR_ONE => '高一',
        self::USER_GRADE_SENIOR_TWO => '高二',
        self::USER_GRADE_SENIOR_THREE => '高三',
        self::USER_GRADE_OTHER => '其他',
    ];

    public static $userSchool2Name = [
        self::USER_SCHOOL_PRIMARY => '小学',
        self::USER_SCHOOL_JUNIOR => '初中',
        self::USER_SCHOOL_SENIOR => '高中',
        self::USER_SCHOOL_OTHER => '其他',
    ];

    public static $userSchoolGrade = [
        self::USER_SCHOOL_PRIMARY => [
            self::USER_GRADE_PRIMARY_ONE,
            self::USER_GRADE_PRIMARY_TWO,
            self::USER_GRADE_PRIMARY_THREE,
            self::USER_GRADE_PRIMARY_FOUR,
            self::USER_GRADE_PRIMARY_FIVE,
            self::USER_GRADE_PRIMARY_SIX,
            self::USER_GRADE_PRIMARY_SEVEN,
            self::USER_GRADE_PRIMARY_EIGHT,
            self::USER_GRADE_PRIMARY_NINE,
        ],
        self::USER_SCHOOL_JUNIOR => [
            self::USER_GRADE_JUNIOR_ONE,
            self::USER_GRADE_JUNIOR_TWO,
            self::USER_GRADE_JUNIOR_THREE,
        ],
        self::USER_SCHOOL_SENIOR => [
            self::USER_GRADE_SENIOR_ONE,
            self::USER_GRADE_SENIOR_TWO,
            self::USER_GRADE_SENIOR_THREE,
        ],
        self::USER_SCHOOL_OTHER => [
            self::USER_GRADE_OTHER,
        ],
    ];

    public static $userGradeLevelMap = [
        self::USER_GRADE_PRIMARY_ONE => 1,
        self::USER_GRADE_PRIMARY_TWO => 3,
        self::USER_GRADE_PRIMARY_THREE => 5,
        self::USER_GRADE_PRIMARY_FOUR => 7,
        self::USER_GRADE_PRIMARY_FIVE => 9,
        self::USER_GRADE_PRIMARY_SIX => 11,
        self::USER_GRADE_PRIMARY_SEVEN => 7,
        self::USER_GRADE_PRIMARY_EIGHT => 9,
        self::USER_GRADE_PRIMARY_NINE => 11,
        self::USER_GRADE_JUNIOR_ONE => 13,
        self::USER_GRADE_JUNIOR_TWO => 15,
        self::USER_GRADE_JUNIOR_THREE => 17,
        self::USER_GRADE_SENIOR_ONE => 19,
        self::USER_GRADE_SENIOR_TWO => 21,
        self::USER_GRADE_SENIOR_THREE => 23,
        self::USER_GRADE_OTHER => 1,
    ];


    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }


}