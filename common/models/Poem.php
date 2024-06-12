<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/25
 * Time: 9:16 PM
 */

namespace common\models;


class Poem extends \common\models\gii\Poem
{
    const POEM_TYPE_POEM = 1;   // 诗
    const POEM_TYPE_POETRY    = 2;   // 词
    const POEM_TYPE_SONG = 3; // 曲
    const POEM_TYPE_MODERN = 4; // 现代诗
    const POEM_TYPE_IDIOM = 5; // 成语

    public static $poemType2Name = [
        self::POEM_TYPE_POEM => '诗',
        self::POEM_TYPE_POETRY    => '词',
        self::POEM_TYPE_SONG => '曲',
        self::POEM_TYPE_MODERN => '现代诗',
        self::POEM_TYPE_IDIOM => '成语',
    ];

    const POEM_CLASS_IDIOM_NORMAL = 500; // 成语普通
    const POEM_CLASS_IDIOM_NUMBER = 501; // 成语数字
    const POEM_CLASS_IDIOM_ANIMAL = 502; // 成语动物
    const POEM_CLASS_IDIOM_PLANT = 503; // 成语植物
    const POEM_CLASS_IDIOM_COLOR = 504; // 成语颜色
    const POEM_CLASS_IDIOM_DOUBLE_WORD = 505; // 成语叠字
    const POEM_CLASS_IDIOM_NOT_WORD = 506; // 成语"不"
    const POEM_CLASS_IDIOM_SAME_WORD = 507; // 成语同字
    const POEM_CLASS_IDIOM_OPPOSITE = 508; // 成语反义
    const POEM_CLASS_IDIOM_OTHER = 509; // 其他

    public static $poemClass2Name = [
        self::POEM_CLASS_IDIOM_NORMAL => '成语普通',
        self::POEM_CLASS_IDIOM_NUMBER => '成语数字',
        self::POEM_CLASS_IDIOM_ANIMAL => '成语动物',
        self::POEM_CLASS_IDIOM_PLANT => '成语植物',
        self::POEM_CLASS_IDIOM_COLOR => '成语颜色',
        self::POEM_CLASS_IDIOM_DOUBLE_WORD => '成语叠字',
        self::POEM_CLASS_IDIOM_NOT_WORD => '成语"不"',
        self::POEM_CLASS_IDIOM_SAME_WORD => '成语同字',
        self::POEM_CLASS_IDIOM_OPPOSITE => '成语反义',
    ];

    const POEM_CLASS2_IDIOM_NUMBER_ONE_TO_TEN  = 50101; // 一到十
    const POEM_CLASS2_IDIOM_NUMBER_HUNDRED     = 50102; // 百
    const POEM_CLASS2_IDIOM_NUMBER_THOUSAND    = 50103; // 千
    const POEM_CLASS2_IDIOM_NUMBER_TEN_THOUSAND = 50104; // 万
    const POEM_CLASS2_IDIOM_NUMBER_HUNDRED_MILLION = 50105; // 亿
    const POEM_CLASS2_IDIOM_NUMBER_OTHER = 50106; // 其他
    const POEM_CLASS2_IDIOM_ANIMAL_MOUSE = 50201; // 鼠
    const POEM_CLASS2_IDIOM_ANIMAL_OX = 50202; // 牛
    const POEM_CLASS2_IDIOM_ANIMAL_TIGER = 50203; // 虎
    const POEM_CLASS2_IDIOM_ANIMAL_RABBIT = 50204; // 兔
    const POEM_CLASS2_IDIOM_ANIMAL_DRAGON = 50205; // 龙
    const POEM_CLASS2_IDIOM_ANIMAL_SNAKE = 50206; // 蛇
    const POEM_CLASS2_IDIOM_ANIMAL_HORSE = 50207; // 马
    const POEM_CLASS2_IDIOM_ANIMAL_SHEEP = 50208; // 羊
    const POEM_CLASS2_IDIOM_ANIMAL_MONKEY = 50209; // 猴
    const POEM_CLASS2_IDIOM_ANIMAL_CHICKEN = 50210; // 鸡
    const POEM_CLASS2_IDIOM_ANIMAL_DOG = 50211; // 狗
    const POEM_CLASS2_IDIOM_ANIMAL_PIG = 50212; // 猪
    const POEM_CLASS2_IDIOM_ANIMAL_BIRD = 50213; // 鸟
    const POEM_CLASS2_IDIOM_ANIMAL_BEAST = 50214; // 兽
    const POEM_CLASS2_IDIOM_ANIMAL_FISH = 50215; // 鱼
    const POEM_CLASS2_IDIOM_ANIMAL_INSECT = 50216; // 虫
    const POEM_CLASS2_IDIOM_ANIMAL_OTHER = 50217; // 其他

    public static $poemClass22Name = [
        self::POEM_CLASS2_IDIOM_NUMBER_ONE_TO_TEN  => '一到十',
        self::POEM_CLASS2_IDIOM_NUMBER_HUNDRED     => '百',
        self::POEM_CLASS2_IDIOM_NUMBER_THOUSAND    => '千',
        self::POEM_CLASS2_IDIOM_NUMBER_TEN_THOUSAND => '万',
        self::POEM_CLASS2_IDIOM_NUMBER_HUNDRED_MILLION => '亿',
        self::POEM_CLASS2_IDIOM_NUMBER_OTHER => '数字其他',
        self::POEM_CLASS2_IDIOM_ANIMAL_MOUSE => '鼠',
        self::POEM_CLASS2_IDIOM_ANIMAL_OX => '牛',
        self::POEM_CLASS2_IDIOM_ANIMAL_TIGER => '虎',
        self::POEM_CLASS2_IDIOM_ANIMAL_RABBIT => '兔',
        self::POEM_CLASS2_IDIOM_ANIMAL_DRAGON => '龙',
        self::POEM_CLASS2_IDIOM_ANIMAL_SNAKE => '蛇',
        self::POEM_CLASS2_IDIOM_ANIMAL_HORSE => '马',
        self::POEM_CLASS2_IDIOM_ANIMAL_SHEEP => '羊',
        self::POEM_CLASS2_IDIOM_ANIMAL_MONKEY => '猴',
        self::POEM_CLASS2_IDIOM_ANIMAL_CHICKEN => '鸡',
        self::POEM_CLASS2_IDIOM_ANIMAL_DOG => '狗',
        self::POEM_CLASS2_IDIOM_ANIMAL_PIG => '猪',
        self::POEM_CLASS2_IDIOM_ANIMAL_BIRD => '鸟',
        self::POEM_CLASS2_IDIOM_ANIMAL_BEAST => '兽',
        self::POEM_CLASS2_IDIOM_ANIMAL_FISH => '鱼',
        self::POEM_CLASS2_IDIOM_ANIMAL_INSECT => '虫',
        self::POEM_CLASS2_IDIOM_ANIMAL_OTHER => '动物其他',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }


}