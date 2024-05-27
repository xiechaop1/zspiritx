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

    public static $poemType2Name = [
        self::POEM_TYPE_POEM => '诗',
        self::POEM_TYPE_POETRY    => '词',
        self::POEM_TYPE_SONG => '曲',
        self::POEM_TYPE_MODERN => '现代诗',
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