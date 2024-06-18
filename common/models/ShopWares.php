<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class ShopWares extends \common\models\gii\ShopWares
{

    const LINK_TYPE_STORY_MODEL = 1;

    public static $linkTypeMap = [
        self::LINK_TYPE_STORY_MODEL => '模型',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getStoryModel(){
        return $this->hasOne('common\models\StoryModels',  ['id' => 'link_id']);
    }


}