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
    const QA_TYPE_WORD = 1;     // 文字题
    const QA_TYPE_PIC = 2;      // 图片题
    const QA_TYPE_VIDEO = 3;    // 视频题



    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior'
            ]
        ];
    }

}