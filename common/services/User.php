<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Qa;
use common\models\Session;
use common\models\UserKnowledge;
use common\models\UserQa;
use common\models\UserScore;
use yii\base\Component;
use yii;

class User extends Component
{
    public function updateUserLevel($userId, $addLevel = 1, $mode = 1) {
        // $mode
        // 1 - 增加等级； 2 - 设置等级
        $userExtends = \common\models\UserExtends::find()
            ->where(['user_id' => $userId])
            ->one();


        $initLevel = 0;
        if (!empty($userExtends->level)) {
            $initLevel = $userExtends->level;
        }
        if ($mode == 1) {
            $targetLevel = $initLevel + $addLevel;
        } else {
            $targetLevel = $addLevel;
        }

        if (empty($userExtends)) {
            $userExtends = new \common\models\UserExtends();
            $userExtends->user_id = $userId;
        }
        $userExtends->level = $targetLevel;
        $ret = $userExtends->save();

        if ($ret !== false) {
            return $userExtends;
        } else {
            throw new \Exception('更新用户等级失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
        }

//        return $userExtends;
    }



}