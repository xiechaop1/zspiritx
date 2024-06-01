<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class StoryStages extends \common\models\gii\StoryStages
{

    const SCAN_TYPE_IMAGE = 1;  // 图像识别
    const SCAN_TYPE_LATLNG = 2; // 经纬度识别

    public static $scanType2Name = [
        self::SCAN_TYPE_IMAGE => '图像识别',
        self::SCAN_TYPE_LATLNG => '经纬度识别',
    ];

    const STAGE_CLASS_NORMAL    = 1; // 普通
    const STAGE_CLASS_EXTEND    = 2; // 扩展

    public static $stageClass2Name = [
        self::STAGE_CLASS_NORMAL => '普通',
        self::STAGE_CLASS_EXTEND => '扩展',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getNextstage() {
        return $this->hasMany('common\models\StoryStageLink', ['pre_story_stage_id' => 'id'])->with('storystage');
    }

    public function getStory() {
        return $this->hasOne('common\models\Story', ['id' => 'story_id']);
    }


}