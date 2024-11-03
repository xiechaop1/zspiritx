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
use common\models\UserData;
use common\models\UserExtends;
use common\models\UserKnowledge;
use common\models\UserQa;
use common\models\UserScore;
use yii\base\Component;
use yii;

class User extends Component
{

    const USER_DATA_TYPE_ADD = 1;
    const USER_DATA_TYPE_UPDATE = 2;

    const USER_DATA_TIME_TYPE_DAY = 1;
    const USER_DATA_TIME_TYPE_WEEK = 2;
    const USER_DATA_TIME_TYPE_MONTH = 3;
    const USER_DATA_TIME_TYPE_YEAR = 4;
    const USER_DATA_TIME_TYPE_TOTAL = 99;


    const MEMBER_LEVEL_NONE = 2;
    const MEMBER_LEVEL_NORMAL = 1;
    const MEMBER_LEVEL_TIMEOUT = 10;

    private $_user = null;

    public static $privilegeName = [
        '1' => '普通用户',
        '2' => 'VIP',
        '3' => 'SVIP',
    ];

    public static $privilege = [
        '-1' => [
            'max_qa_each' => -1,
            'max_qa' => -1,
        ],
        '0' => [
            'max_qa_each' => 30,
            'max_qa' => 120,
        ],
    ];

    public function getUserMemberPrivilege($userId) {
        $userMember = $this->getUserMemberLevel($userId);

        $ret = [];

        if (!empty($userMember['memberStatus'])) {
            if ($userMember['memberStatus'] == self::MEMBER_LEVEL_NONE
                || $userMember['memberStatus'] == self::MEMBER_LEVEL_TIMEOUT
            ) {
                $privilege = self::$privilege[0];
            }

        }

        // 无限制
        $privilege = self::$privilege[-1];

        $ret = [
            'userMember' => $userMember,
            'privilege' => $privilege,
        ];

        return $ret;
    }

    public function getUserMemberLevel($userId) {

        if (empty($userId)) {
            return false;
        }

        if (empty($this->_user)) {
            $user = \common\models\User::find()
                ->where(['id' => $userId])
                ->one();
            $this->_user = $user;
        } else {
            $user = $this->_user;
        }


        $memberStatus = self::MEMBER_LEVEL_NONE;
        if (!empty($user)) {
            if (!empty($user->member_expire_at) && $user->member_expire_at < time()) {
                $memberStatus = self::MEMBER_LEVEL_TIMEOUT;
            } else if (empty($user->member_expire_at)) {
                $memberStatus = self::MEMBER_LEVEL_NONE;
            } else {
                $memberStatus = self::MEMBER_LEVEL_NORMAL;
            }

            if ($memberStatus == self::MEMBER_LEVEL_NORMAL) {
                if (empty($user->member_level)) {
                    $memberStatus = self::MEMBER_LEVEL_NONE;
                }
            }
        }

        $ret = [
            'memberStatus' => $memberStatus,
            'memberLevel' => $user->member_level,
            'memberExpireAt' => $user->member_expire_at,
        ];

        return $ret;
    }

    public function updateUserLevelWithRight($userId, $subjCt = 0, $rightCt = 0) {
        $ret = [];
        if ($subjCt > 0) {
            if (($rightCt / $subjCt) > 0.8) {
                $addLevel = 1;
                $ret = $this->updateUserLevel($userId, $addLevel);
            } elseif (($rightCt / $subjCt) < 0.4) {
                $addLevel = -1;
                $ret = $this->updateUserLevel($userId, $addLevel);
            }
        }
        return $ret;
    }

    public function updateUserLevel($userId, $addLevel = 1, $mode = 1) {
        // $mode
        // 1 - 增加等级； 2 - 设置等级
        $userExtends = \common\models\UserExtends::find()
            ->where(['user_id' => $userId])
            ->one();

//        var_dump($userExtends);

        $initLevel = 0;
        if (!empty($userExtends->level)) {
            $initLevel = $userExtends->level;
        }
        if ($mode == 1) {
            $targetLevel = $initLevel + $addLevel;
            if ($targetLevel < 1) {
                $targetLevel = 1;
            }
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

    public function updateUserData($userId, $storyId, $dataType, $value, $addType = self::USER_DATA_TYPE_ADD, $timeType = self::USER_DATA_TIME_TYPE_DAY) {

        switch ($timeType) {
            case self::USER_DATA_TIME_TYPE_DAY:
            default:
                $beginTs = strtotime(Date('Y-m-d 00:00:00'));
                $endTs = strtotime(Date('Y-m-d 23:59:59'));
                break;
        }

        $userData = UserData::find()
            ->where([
                'user_id' => $userId,
                'story_id' => $storyId,
                'data_type' => $dataType,
            ]);
        if ($timeType != self::USER_DATA_TIME_TYPE_TOTAL) {
            $userData->andFilterWhere(['between', 'created_at', $beginTs, $endTs]);
        }
        $userData = $userData->one();

        if (empty($userData)) {
            $userData = new UserData();
            $userData->user_id = $userId;
            $userData->story_id = $storyId;
            $userData->data_type = (string)$dataType;
            $userData->data_value = (string)$value;
            $userData->data_date = Date('Y-m-d');
            $userData->time_type = (string)$timeType;
        } else {
            if ($addType == self::USER_DATA_TYPE_ADD) {
                $tmpValue = $userData->data_value;
                $tmpValue += $value;
                $userData->data_value = (string)$tmpValue;
            } else {
                $userData->data_value = (string)$value;
            }
        }
        $ret = $userData->save();
        if ($ret !== false) {
            return $userData;
        } else {
            var_dump($userData->getErrors());
            throw new \Exception('更新用户数据失败', ErrorCode::USER_DATA_UPDATE_FAILED);
        }
    }

    public function getUserExtendsModel($userId) {
        $userExtends = \common\models\UserExtends::find()
            ->where(['user_id' => $userId])
            ->one();
        if (empty($userExtends)) {
            $userExtends = new \common\models\UserExtends();
            $userExtends->user_id = $userId;
        }
        return $userExtends;
    }

    /**
     * @param $userId
     * @param $qa
     * @param $ct
     * @param $ctType ( 1 - 加分（正确）； 2 - 减分（错误）)
     * @return array|\common\models\UserExtends|false|yii\db\ActiveRecord|null
     * @throws \Exception
     */
    public function updateUserExtendsWithQaProp($userId, $qa, $ct, $ctType = 1) {

        $points = [];
        if (!empty($qa->prop)) {
            $qaProp = json_decode($qa->prop, true);
            if (!empty($qaProp['point'])) {
                $points = $qaProp['point'];
            }
        }
        if (empty($points)) {
            return false;
        }

        $userExtends = $this->getUserExtendsModel($userId);

        $userProp = !empty($userExtends->prop) ? json_decode($userExtends->prop, true) : [];
        if (empty($userProp)) {
            $userProp = [];
        }
        $allUserProps = UserExtends::$allUserProps;

        foreach ($allUserProps as $pointId) {
            if (in_array($pointId, $points)) {

                if (!isset($userProp[$pointId])) {
                    $userProp[$pointId] = 50;
                }
                if ($ctType == 1) {
                    $userProp[$pointId] += $ct;
                    if ($userProp[$pointId] > 100) {
                        $userProp[$pointId] = 100;
                    }
                } else {
                    $userProp[$pointId] -= $ct;
                    if ($userProp[$pointId] < 0) {
                        $userProp[$pointId] = 0;
                    }
                }
            } else {
                if (!isset($userProp[$pointId])) {
                    $userProp[$pointId] = 50;
                }
            }
        }
        $userExtends->prop = json_encode($userProp, JSON_UNESCAPED_UNICODE);

        $ret = $userExtends->save();
        if ($ret !== false) {
            return $userExtends;
        } else {
            throw new \Exception('更新用户数据失败', ErrorCode::USER_DATA_UPDATE_FAILED);
        }
    }

}