<?php
/**
 * Created by PhpStorm.
 * User: choiceGroup
 * Date: 2023/5/18
 * Time: 下午6:06
 */

namespace common\models;


use common\definitions\Common;

class UserWare extends \common\models\gii\UserWare
{

    const USER_WARE_STATUS_NORMAL = 0;

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ]
        ];
    }

    public function exec() {

        $ret = $this->save();
        return $ret;
    }

    public function getStory(){
        return $this->hasOne('common\models\Story',  ['id' => 'story_id']);
    }

    public function getShopWare(){
        return $this->hasOne('common\models\ShopWares',  ['id' => 'ware_id']);
    }


}