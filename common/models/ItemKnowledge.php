<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class ItemKnowledge extends \common\models\gii\ItemKnowledge
{
    const ITEM_TYPE_QA      = 1; // 问答
    const ITEM_TYPE_STAGE   = 1;    // 场景

    public static $itemType2Name = [
        self::ITEM_TYPE_QA => '问答',
        self::ITEM_TYPE_STAGE => '场景',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getKnowledge() {
        return $this->hasOne('common\models\Knowledge', ['id' => 'knowledge_id']);
    }

    public function getStory() {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }


}