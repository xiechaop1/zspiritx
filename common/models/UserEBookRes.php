<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


class UserEBookRes extends \common\models\gii\UserEbookRes
{

    public $is_show;

    const USER_EBOOK_RES_STATUS_DEFAULT = 0; // 默认
    const USER_EBOOK_RES_STATUS_VIDEO_GENERATE = 10; // 视频生成中
    const USER_EBOOK_RES_STATUS_VIDEO_GENERATE_SUCCESS = 11; // 视频生成成功
    const USER_EBOOK_RES_STATUS_VIDEO_GENERATE_FAIL = 12; // 视频生成失败
    const USER_EBOOK_RES_STATUS_COMPLETED = 20; // 完成

    const USER_EBOOK_RES_STATUS_VIDEO_CANCEL = 18;      // 视频生成取消

    public static $userEbookStatus2Name = [
        self::USER_EBOOK_RES_STATUS_DEFAULT => '默认',
        self::USER_EBOOK_RES_STATUS_VIDEO_GENERATE => '视频生成中',
        self::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_SUCCESS => '视频生成成功',
        self::USER_EBOOK_RES_STATUS_VIDEO_GENERATE_FAIL => '视频生成失败',
        self::USER_EBOOK_RES_STATUS_VIDEO_CANCEL => '视频生成取消',
        self::USER_EBOOK_RES_STATUS_COMPLETED => '完成',
    ];

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function getUser() {
        return $this->hasOne('common\models\User',  ['id' => 'user_id']);
    }

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getUserEBook() {
        return $this->hasOne('common\models\UserEBook', ['id' => 'user_ebook_id']);
    }


}