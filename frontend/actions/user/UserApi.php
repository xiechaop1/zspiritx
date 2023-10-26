<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\user;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\helpers\Cookie;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\StoryStages;
use common\models\User;
//use liyifei\base\actions\ApiAction;
use common\models\UserList;
use common\models\UserScore;
use common\models\UserStory;
use common\models\UserLoc;
use frontend\actions\ApiAction;
use yii;

class UserApi extends ApiAction
{
    public $action;

    public $userId;

    private $_get;

    public function run()
    {
        try {
            $this->_get = Yii::$app->request->get();
            switch ($this->action) {
                case 'new_user':
                    $ret = $this->newUser();
                    break;
                case 'get_user':
                    $ret = $this->getUser();
                    break;
                case 'get_wx_session':
                    $ret = $this->getWxSession();
                    break;
                case 'get_mobile':
                    $ret = $this->getMobile();
                    break;
                case 'get_token':
                    $ret = $this->getToken();
                    break;
                case 'login':
                    $ret = $this->login();
                    break;
                case 'login_and_reg_by_mobile':
                    $ret = $this->loginAndRegByMobile();
                    break;
                case 'update_user':
                    $this->valToken();
                    $ret = $this->updateUser();
                    break;

                case 'update_user_loc':
                    $ret = $this->updateUserLoc();
                    break;
                case 'update_user_stage':
                    $ret = $this->updateUserStage();
                    break;
                case 'get_user_loc':
                    $ret = $this->getUserLoc();
                    break;
                case 'get_user_loc_by_team':
                    $ret = $this->getUserLocByTeam();
                    break;
                case 'get_user_list_by_session':
                    $ret = $this->getUserListBySession();
                    break;
                case 'get_user_list_by_story':
                    $ret = $this->getUserListByStory();
                    break;
                case 'get_user_list_by_team':
                    $ret = $this->getUserListByTeam();
                    break;
                case 'get_user_score_rank':
                    $ret = $this->getUserScoreRank();
                    break;
                case 'add_user_score':
                    $ret = $this->addUserScore();
                    break;
                case 'get_user_score':
                    $ret = $this->getUserScore();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }

        return $this->success($ret);
    }

    public function login() {
        $userName = !empty($this->_get['user_name']) ? $this->_get['user_name'] : '';
        $userPass = !empty($this->_get['user_pass']) ? $this->_get['user_pass'] : '';

        if (empty($userName) || empty($userPass)) {
            throw new \Exception('用户名或密码不能为空', ErrorCode::USER_PARAMETERS_INVALID);
        }

        $userInfo = User::findOne([
            'user_name' =>  $userName,
            'user_pass' =>  Yii::$app->security->generatePasswordHash($userPass),
//            'user_status'   => User::USER_STATUS_NORMAL,
        ]);

        if (empty($userInfo)) {
            throw new \Exception('用户名或密码错误', ErrorCode::USER_PARAMETERS_INVALID);
        }

        if ($userInfo['user_status'] == User::USER_STATUS_FORBIDDEN) {
            throw new \Exception('用户已被禁用', ErrorCode::USER_FORBIDDEN);
        }

        $_SESSION['user_info'] = $userInfo;

        return $userInfo;
    }

    public function loginAndRegByMobile() {
        $mobile = !empty($this->_get['mobile']) ? $this->_get['mobile'] : '';
        $verifyCode = !empty($this->_get['verify_code']) ? $this->_get['verify_code'] : '';
//        $userPass = !empty($this->_get['user_pass']) ? $this->_get['user_pass'] : '';

        if (empty($mobile)
//            || empty($userPass)
        ) {
            throw new \Exception('手机号或密码不能为空', ErrorCode::USER_PARAMETERS_INVALID);
        }

        if ($mobile != '1' && !YII_DEBUG) {
            $verifyVal = Yii::$app->verificationCode->validate($mobile, $verifyCode, \common\definitions\VerificationCode::TYPE_REGISTER);
            if (!$verifyVal) {
                throw new \Exception('验证码错误', ErrorCode::USER_PARAMETERS_INVALID);
            }
        }

        $userInfo = User::findOne([
            'mobile' =>  $mobile,
//            'user_pass' =>  Yii::$app->security->generatePasswordHash($userPass),
//            'user_status'   => User::USER_STATUS_NORMAL,
        ]);

        if (empty($userInfo)) {
            $userInfo = new User();
            $userInfo->mobile = $mobile;
            $userInfo->user_name = '玩家' . substr($mobile, strlen($mobile) - 4, 4) . rand(1000,9999);
            $userInfo->user_pass = Yii::$app->security->generatePasswordHash($mobile);
            $userInfo->user_status = User::USER_STATUS_NORMAL;
            $userInfo->save();
            $userModel['id'] = Yii::$app->db->getLastInsertId();
        } else {

            if ($userInfo['user_status'] == User::USER_STATUS_FORBIDDEN) {
                throw new \Exception('用户已被禁用', ErrorCode::USER_FORBIDDEN);
            }
        }

        $_SESSION['user_info'] = $userInfo;
        Cookie::setCookie('user_id', $userInfo['id'], 3600 * 24 * 30);

        return $userInfo;
    }

    public function wxlogin() {
        $code = $this->_get['code'];
        $userInfo = $this->_get['user_info'];
        try {
//            $ret = Yii::$app->wechat->login($code);
            $ret = Yii::$app->wechat->getSession($code);
            $openId = $ret['openid'];
            $sessionKey = $ret['session_key'];

            if (!empty($userInfo)) {
//                $userInfo = json_decode($userInfo, true);
                $encryptedData = $userInfo['encryptedData'];
                $iv = $userInfo['iv'];
                $decipher = Yii::$app->wechat->decryptData($encryptedData, $iv, $sessionKey);

                $ret = $decipher;
            }

//            Yii::$app->oplog->write(\common\models\Log::OP_CODE_LOGIN, 1, $this->_userId, 0, '用户' . $this->_userId . '登录');
//            return $this->success($ret);
            return $ret;
        } catch (\Exception $e) {
//            Yii::$app->oplog->write(\common\models\Log::OP_CODE_LOGIN, 0, $this->_userId, 0, '用户' . $this->_userId . '登录');
//            return $this->fail($e->getCode() . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function getSelfToken() {

    }

    public function getSessionUser() {
        return $_SESSION['user_info'];
    }

    public function newUser() {
        $userName = !empty($this->_get['user_name']) ? $this->_get['user_name'] : '';
//        $nickName = !empty($this->_get['nick_name']) ? $this->_get['nick_name'] : '';
        $userInfo = User::findOne(['user_name' => $userName]);
        if (!empty($userInfo)) {
            throw new \Exception('用户名已存在', ErrorCode::USER_EXIST);
        }

        $userPass = !empty($this->_get['user_pass']) ? $this->_get['user_pass'] : '';
        $avatar = !empty($this->_get['avatar']) ? $this->_get['avatar'] : '';
        $mobile = !empty($this->_get['mobile']) ? $this->_get['mobile'] : '';
        $geoLat = !empty($this->_get['lat']) ? $this->_get['lat'] : '';
        $geoLng = !empty($this->_get['lng']) ? $this->_get['lng'] : '';

        $userModel = new User();
        $userModel->user_name = $userName;
        $userModel->user_pass = Yii::$app->security->generatePasswordHash($userPass);
        $userModel->avatar = $avatar;
        $userModel->mobile = $mobile;
        $userModel->last_login_geo_lat = $geoLat;
        $userModel->last_login_geo_lng = $geoLng;
        $userModel->user_status = User::USER_STATUS_NORMAL;
        try {
            $ret = $userModel->save();
            $userModel['id'] = Yii::$app->db->getLastInsertId();

            return $userModel;
        } catch (\Exception $e) {
//            var_dump($e);
            throw new \Exception('注册失败', ErrorCode::USER_REGISTER_FAIL);
        }
    }

    public function getToken() {
        try {

            $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
            $ret = null;
            if (!empty($userId)) {
                $userInfo = User::findOne($userId);

                if (!empty($userInfo['wx_token'])
                    && !empty($userInfo['wx_token_expire_time'])
                    && $userInfo['wx_token_expire_time'] > time()) {
                    $ret = [
                        'token' => $userInfo['wx_token'],
                        'expire_time' => $userInfo['wx_token_expire_time'],
                    ];
                } else {
                    $tokenRet = Yii::$app->wechat->getToken();
                    $ret['token'] = $userInfo['wx_token'] = $tokenRet['access_token'];
                    $ret['expire_time'] = $userInfo['wx_token_expire_time'] = time() + $tokenRet['expires_in'];
                    $userInfo->save();
                }

            }

//            return $this->success($ret);
            return $ret;
        } catch (\Exception $e) {
            throw $e;
//            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }
    }

    public function getWxSession() {
        $code = $this->_get['code'];
        try {
            $ret = Yii::$app->wechat->getSession($code);

            $openId = $ret['openid'];
            $user = User::findOne(['wx_openid' => $openId, 'is_delete' => Common::STATUS_NORMAL]);
            if (!empty($user)
                && $user->user_status == User::USER_STATUS_FORBIDDEN
            ) {
                throw new \Exception('用户已被禁用', -1001);
            }
            if (!empty($user['id'])) {
                $this->_get['user_id'] = $user['id'];
                $tokenRet = $this->getToken();
                $user['wx_token'] = $tokenRet['token'];
                $user['wx_token_expire_time'] = $tokenRet['expire_time'];

                Yii::$app->oplog->write(\common\models\Log::OP_CODE_LOGIN, 1, $user['id'], 0, '用户登录');
            }
            $ret['user'] = $user;

            return $ret;
        } catch (\Exception $e) {
            throw $e;
//            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }
    }

    public function getMobile() {
        $code = $this->_get['code'];
        $openId = $this->_get['open_id'];
        $unionId = !empty($this->_get['union_id']) ? $this->_get['union_id'] : '';
        try {
            $mobile = Yii::$app->wechat->getMobile($code);
            $user = null;
            if (!empty($mobile)) {
                $user = User::findOne(['mobile' => $mobile, 'is_delete' => Common::STATUS_NORMAL]);

                // 判断用户状态（是不是在白名单里，也就是状态是"被邀请"）
                if (empty($user)
                    || $user->user_status == User::USER_STATUS_FORBIDDEN
                ) {
                    throw new \Exception('用户不存在或已被禁用', -1001);
                } else {
                    $userInfo = !empty($this->_get['user_info']) ? json_decode($this->_get['user_info'], true) : [];
                    $user->user_name = !empty($userInfo['nickName']) ? $userInfo['nickName'] : '';
                    $user->avatar = !empty($userInfo['avatarUrl']) ? $userInfo['avatarUrl'] : '';

                    $user->wx_openid = $openId;
                    $user->wx_unionid = $unionId;
                    $user->user_status = User::USER_STATUS_NORMAL;

                }
                $tokenRet = Yii::$app->wechat->getToken();
                $user->wx_token = !empty($tokenRet['access_token']) ? $tokenRet['access_token'] : '';
                $user->wx_token_expire_time = !empty($tokenRet['expires_in']) ? time() + $tokenRet['expires_in'] : '';
                $user->save();
                Yii::$app->oplog->write(\common\models\Log::OP_CODE_REGISTER, 1, $user->id, 0, '获取用户手机号和微信信息');

            }
            return $user;
        } catch (\Exception $e) {
            throw $e;
//            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }
    }

    public function getUser() {


        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

        $retModel = User::find()->where(['id' => $userId]);

        $ret = $retModel->one();

        return $ret;
    }

    public function updateUser() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

        $user = \common\models\User::findOne($userId);

        if (empty($user)) {
            return $this->fail('用户不存在', -100);
        }

        $user->load(['User' => $this->_get]);
        try {
            $ret = $user->save();
            return $user->toArray();
        } catch (\Exception $e) {
            throw $e;
//            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }
    }

    public function updateUserStage() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $storyStageId = !empty($this->_get['story_stage_id']) ? $this->_get['story_stage_id'] : 0;
        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        $storyStage = StoryStages::find()
            ->where([
                'id'    => $storyStageId
            ])
            ->one();

        if (!empty($storyStage)) {
            $stageName = $storyStage->stage_name;
        } else {
            $stageName = '未知之地';
        }

        // 更新任务
        try {
            Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, '进入新场景：' . $stageName, Actions::ACTION_TYPE_MSG);
            Yii::$app->knowledge->setByItem($storyStageId, ItemKnowledge::ITEM_TYPE_STAGE, $sessionId, $sessionStageId, $userId, $storyId);
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    public function updateUserLoc() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $lng = !empty($this->_get['lng']) ? $this->_get['lng'] : 0;
        $lat = !empty($this->_get['lat']) ? $this->_get['lat'] : 0;

        $userLoc = UserLoc::findOne(['user_id' => $userId]);

        if (empty($userLoc)) {
            $userLoc = new UserLoc();
            $userLoc->user_id = $userId;
        }

        $userLoc->lng = $lng;
        $userLoc->lat = $lat;

        try {
            $ret = $userLoc->save();
            return $userLoc;
        } catch (\Exception $e) {
            throw $e;
//            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }
    }

    public function getUserLocByTeam() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $teamId = !empty($this->_get['team_id']) ? $this->_get['team_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : '';

        $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;
        $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;
        $disRange = !empty($this->_get['dis_range']) ? $this->_get['dis_range'] : 0;

        $userStory = UserStory::find();
        if (!empty($sessionId)) {
            $userStory = $userStory->andFilterWhere(['session_id' => $sessionId]);
        }
        if (!empty($teamId)) {
            $userStory = $userStory->andFilterWhere(['team_id' => $teamId]);
        }
        $userStory = $userStory->all();

        $userIds = [$userId];
        foreach ($userStory as $us) {
            if ($us->user_id == $userId) {
                continue;
            }
            $userIds[] = $us->user_id;
        }

        if ($disRange > 0) {
            $sql = 'SELECT *, st_distance(point(lng, lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 as dist FROM o_user_loc WHERE user_id IN (' . implode(',', $userIds) . ')';
            $sql .= ' AND st_distance(point(lng, lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 < ' . $disRange;
            $sql .= ' ORDER BY dist ASC;';
//            var_dump($sql);
            $userTeamLoc = Yii::$app->db->createCommand($sql)->queryAll();

        } else {
            $userTeamLoc = \common\models\UserLoc::find()
                ->where(['user_id' => $userIds]);
            $userTeamLoc = $userTeamLoc->all();

        }

        return $userTeamLoc;
    }

    public function getUserLoc() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;
        $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;
        $disRange = !empty($this->_get['dis_range']) ? $this->_get['dis_range'] : 0;

        if ($disRange > 0) {
            $sql = 'SELECT *, st_distance(point(lng, lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 as dist FROM o_user_loc WHERE user_id = ' . $userId;
            $sql .= ' AND st_distance(point(lng, lat), point(' . $userLng . ', ' . $userLat . ')) * 111195 < ' . $disRange;
            $sql .= ' ORDER BY dist ASC;';
//            var_dump($sql);
            $userLoc = Yii::$app->db->createCommand($sql)->queryOne();
        } else {
            $userLoc = \common\models\UserLoc::findOne($userId);
        }

        return $userLoc;
    }

    public function getUserListBySession() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : '';

        $ret = UserStory::find()
            ->where(['session_id' => $sessionId])
            ->with('user')
            ->asArray()
            ->all();

        return $ret;
    }

    public function getUserListByStory() {
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : '';

        $ret = UserStory::find()
            ->where(['story_id' => $storyId])
            ->with('user')
            ->asArray()
            ->all();

        return $ret;
    }

    public function getUserListByTeam() {
        $teamId = !empty($this->_get['team_id']) ? $this->_get['team_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        $ret = UserStory::find()
            ->where(['session_id' => $sessionId, 'story_id' => $storyId]);
        if ($teamId > 0) {
            $ret->andFilterWhere(['team_id' => $teamId]);
        }
        $ret = $ret->with(['user', 'userLoc'])
            ->asArray()
            ->all();

        return $ret;
    }

    public function addUserScore() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $teamId = !empty($this->_get['team_id']) ? $this->_get['team_id'] : 0;
        $score = !empty($this->_get['score']) ? $this->_get['score'] : 0;

        try {
            $userScore = UserScore::find()
                ->where([
                    'user_id' => $userId,
                    'story_id' => $storyId,
                    'session_id' => $sessionId,
                ]);

            if (!empty($teamId)) {
                $userScore = $userScore->andFilterWhere(['team_id' => $teamId]);
            }

            $userScore = $userScore->one();

            if (empty($userScore)) {
                $userScore = new UserScore();
                $userScore->user_id = $userId;
                $userScore->story_id = $storyId;
                $userScore->session_id = $sessionId;
                $userScore->team_id = $teamId;
                $userScore->score = $score;
            } else {
                $userScore->score = $userScore + $score;
            }
            $ret = $userScore->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return $ret;
    }

    public function getUserScore() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $teamId = !empty($this->_get['team_id']) ? $this->_get['team_id'] : 0;

        $ret = UserScore::find()
            ->where([
                'user_id' => $userId,
                'story_id' => $storyId,
                'session_id' => $sessionId,
            ]);

        if (!empty($teamId)) {
            $ret = $ret->andFilterWhere(['team_id' => $teamId]);
        }
        $ret = $ret->one();

        return $ret;
    }

    public function getUserScoreRank() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $teamId = !empty($this->_get['team_id']) ? $this->_get['team_id'] : 0;

        $ret = UserScore::find()
            ->where([
                'user_id'   => $userId,
                'story_id'  => $storyId,
            ]);

        if (!empty($sessionId)) {
            $ret = $ret->andFilterWhere(['session_id' => $sessionId]);
        }

        if (!empty($teamId)) {
            $ret = $ret->andFilterWhere(['team_id' => $teamId]);
        }

        $ret = $ret->orderBy('score desc')->asArray()->all();

        return $ret;

    }



}