<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\lottery;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\LotteryPrize;
use common\models\Qa;
use common\models\SessionQa;
use common\models\StoryStages;
use common\models\UserKnowledge;
use common\models\UserLottery;
use common\models\UserModels;
use common\models\UserPrize;
use common\models\UserQa;
use common\models\User;
//use liyifei\base\actions\ApiAction;
use common\models\UserList;
use common\models\UserScore;
use common\models\UserStory;
use frontend\actions\ApiAction;
use yii;

class LotteryApi extends ApiAction
{
    public $action;

//    public $userId;

    private $_storyId;

    private $_story;

    private $_get;

    public function run()
    {
        $this->_get = Yii::$app->request->get();


        try {
            $this->_storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
            if (empty($this->_storyId)
                && $this->action != 'get_user_lottery'
                && $this->action != 'get_user_prize'
            ) {
                throw new \Exception('剧本不存在', ErrorCode::STORY_NOT_FOUND);
            }

            switch ($this->action) {
                case 'award_by_seed':
                    $ret = $this->awardBySeed();
                    break;
                case 'award':
                    $ret = $this->award();
                    break;
                case 'award_by_h5':
                    $ret = $this->awardByH5();
                    break;
                case 'generate_lottery':
                    $ret = $this->generateLottery();
                    break;
                case 'get_user_lottery':
                    $ret = $this->getUserLottery();
                    break;
                case 'get_user_prize':
                    $ret = $this->getUserPrize();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }

        return $this->success($ret);
    }

    public function awardBySeed() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $lotteryId = !empty($_GET['lottery_id']) ? $_GET['lottery_id'] : 1;

        $prizeId = !empty($_GET['prize_id']) ? $_GET['prize_id'] : 0;

        $userPrize = Yii::$app->lottery->getUserPrize($userId, $lotteryId, $sessionId, $storyId, UserPrize::$normalUserPrizeStatus);

        $newUserPrize = Yii::$app->lottery->add($userId, $sessionId, $channelId, $storyId,
            $lotteryId, 0, 0, $prizeId, LotteryPrize::PRIZE_TYPE_GOODS, 0, UserPrize::USER_PRIZE_AWARD_METHOD_EXCHANGE);

        $ret = [
            'user_prize_list' => $userPrize,
            'new_user_prize' => $newUserPrize,
        ];

        return $ret;
    }

    // 抽奖
    public function award() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $lotteryId = !empty($_GET['lottery_id']) ? $_GET['lottery_id'] : 1;
        $userLotteryId = !empty($_GET['user_lottery_id']) ? $_GET['user_lottery_id'] : 0;

        $optCt = !empty($_GET['opt_ct']) ? $_GET['opt_ct'] : 0;

        try {
            $retTemp = Yii::$app->lottery->run($userId, $userLotteryId, $storyId, $sessionId, $lotteryId, $channelId, $optCt);

            $ret = [
                'result' => [
                    'lottery_id' => !empty($retTemp['lottery']['id']) ? $retTemp['lottery']['id'] : 0,
                    'lottery_name' => !empty($retTemp['lottery']['lottery_name']) ? $retTemp['lottery']['lottery_name'] : '',
                    'prize_id' => !empty($retTemp['finalPrize']['id']) ? $retTemp['finalPrize']['id'] : 0,
                    'prize_name' => !empty($retTemp['finalPrize']['prize_name']) ? $retTemp['finalPrize']['prize_name'] : '',
                    'image' => !empty($retTemp['finalPrize']['image']) ? $retTemp['finalPrize']['image'] : '',
                    'prize_level' => !empty($retTemp['finalPrize']['prize_level']) ? $retTemp['finalPrize']['prize_level'] : '',
                    'user_prize_id' => !empty($retTemp['newUserPrize']['id']) ? $retTemp['newUserPrize']['id'] : 0,
                    'user_prize_no' => !empty($retTemp['newUserPrize']['user_prize_no']) ? $retTemp['newUserPrize']['user_prize_no'] : '',
                    'user_prize_status' => !empty($retTemp['newUserPrize']['user_prize_status']) ? $retTemp['newUserPrize']['user_prize_status'] : 0,
                    'user_prize_status_name' => !empty(UserPrize::$userPrizeStatus2Name[$retTemp['newUserPrize']['user_prize_status']]) ?
                        UserPrize::$userPrizeStatus2Name[$retTemp['newUserPrize']['user_prize_status']] : '',
                    'user_prize_expire_time' => !empty($retTemp['newUserPrize']['user_prize_expire_time']) ? $retTemp['newUserPrize']['user_prize_expire_time'] : 0,
                    'user_prize_ct' => !empty($retTemp['newUserPrize']['ct']) ? $retTemp['newUserPrize']['ct'] : 0,
                    'desc' => '奖品兑奖地址：国家植物园科普馆门口\n兑奖截止时间：2024年05月01日 0点之前\n本次活动最终解释权归 公园加 所有',
                ]
            ];
//            $ret += $retTemp;

        } catch (\Exception $e) {
            throw $e;
        }

        return $ret;
    }


    // 抽奖
    public function awardByH5() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $lotteryId = !empty($_GET['lottery_id']) ? $_GET['lottery_id'] : 1;
        $userLotteryId = !empty($_GET['user_lottery_id']) ? $_GET['user_lottery_id'] : 0;

        $optCt = !empty($_GET['opt_ct']) ? $_GET['opt_ct'] : 0;

        $storyModelId = !empty($_GET['story_model_id']) ? $_GET['story_model_id'] : 0;

        try {

            if ( empty($optCt) ) {

                $userKnowledge = UserKnowledge::find()
                    ->where([
                        'user_id' => $userId,
//                        'story_id' => $storyId,
                        'session_id'    => $sessionId,
                        'knowledge_status' => UserKnowledge::KNOWLDEGE_STATUS_COMPLETE,
                    ])
                    ->all();

                if (!empty($userKnowledge)) {
                    $tmpUk = [];
                    $ct = 0;
                    foreach ($userKnowledge as $uk) {
                        if (empty($tmpUk[$uk->id])) {
                            $tmpUk[$uk->id] = 1;
                            $ct++;
                        }
                    }
                    $optCt = $ct;
                }

            }


            $ret = Yii::$app->lottery->run($userId, $userLotteryId, $storyId, $sessionId, $lotteryId, $channelId, $optCt);

            if (!empty($ret) && $ret['isAward'] == 1) {
                // Todo 待优化
                // 植物园写死直接加入兑奖券（380）
                // 后续优化
                $newUserModel = Yii::$app->baggage->pickup($storyId, $sessionId, 380, $userId, 1);
            }


            if (!empty($storyModelId)) {
                $userModelBaggage = UserModels::find()
                    ->where([
                        'user_id'           => (int)$userId,
                        'session_id'        => (int)$sessionId,
                        'story_model_id'    => (int)$storyModelId,
                    ])
                    ->one();

                $userLotteryCt = UserLottery::find()
                    ->where([
                        'user_id'   => $userId,
                        'lottery_id'    => $lotteryId,
                        'session_id'    => $sessionId,
                        'story_id'      => $storyId,
                    ])
                    ->andFilterWhere([
                        '>', 'ct', 0
                    ])
                    ->andFilterWhere([
                        'or',
                        ['>=', 'expire_time', time()],
                        ['expire_time' => 0]
                    ])
                    ->andFilterWhere([
                        'lottery_status' => UserLottery::USER_LOTTERY_STATUS_WAIT
                    ])
                    ->count();

                if (!empty($userModelBaggage)) {
                    $userModelBaggage->use_ct = $userLotteryCt;
                    if ($userLotteryCt == 0) {
                        $userModelBaggage->is_delete = Common::STATUS_DELETED;;
                    }
                    $userModelBaggage->save();
                }

            }

        } catch (\Exception $e) {
            throw $e;
        }

        return $ret;
    }

    public function generateLottery() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $lotteryId = !empty($_GET['lottery_id']) ? $_GET['lottery_id'] : 1;
        $ct = !empty($_GET['ct']) ? $_GET['ct'] : 1;

        try {
            $ret = Yii::$app->lottery->generateLottery($userId, $storyId, $sessionId, $lotteryId, $channelId, $ct);
        } catch (\Exception $e) {
            throw $e;
        }

        return $ret;
    }

    public function getUserPrize() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $lotteryId = !empty($_GET['lottery_id']) ? $_GET['lottery_id'] : 1;

        try {
            $tempRet = Yii::$app->lottery->getUserPrize($userId, $lotteryId, $sessionId, $storyId, UserPrize::$allUserPrizeStatus);
            $ret = [];
            if (!empty($tempRet)) {
                foreach ($tempRet as $k => $v) {
                    $one = $v->toArray();
                    $one['user_prize_status_name'] = !empty(UserPrize::$userPrizeStatus2Name[$v->user_prize_status]) ?
                        UserPrize::$userPrizeStatus2Name[$v->user_prize_status] : '';
                    $one['prize'] = $v->prize;
                    $ret[] = $one;
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $ret;
    }



}