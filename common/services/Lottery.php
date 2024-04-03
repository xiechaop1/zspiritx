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
use common\models\LotteryPrize;
use common\models\Qa;
use common\models\Session;
use common\models\UserKnowledge;
use common\models\UserLottery;
use common\models\UserPrize;
use common\models\UserQa;
use common\models\UserScore;
use yii\base\Component;
use yii;
use yii\web\NotFoundHttpException;

class Lottery extends Component
{
    public function run($userId, $userLotteryId, $storyId, $sessionId, $lotteryId, $channelId, $optCt) {
        $lottery = \common\models\Lottery::find()
            ->where([
                'id'    => $lotteryId
            ])
            ->one();

        if (empty($lottery)) {
            throw new NotFoundHttpException('Lottery not found');
        }

        $userLottery = UserLottery::find()
            ->where([
                'id'    => $userLotteryId,
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
            ->one();

        if (empty($userLottery)) {
            throw new \Exception('您的抽奖机会已经用过，或者已经过期/作废，无法抽奖！', ErrorCode::USER_LOTTERY_NOT_FOUND);
        }

        $lotteryPrize = LotteryPrize::find()
            ->where([
                'lottery_id'    => $lotteryId,
            ])
            ->orderBy([
                'prize_level'   => SORT_DESC
            ])
            ->all();

        $userPrize = $this->getUserPrize($userId, $lotteryId, $sessionId, $storyId, UserPrize::$normalUserPrizeStatus);

//        $userPrize = UserPrize::find()
//            ->where([
//                'user_id'   => $userId,
//                'lottery_id'    => $lotteryId,
//                'session_id'    => $sessionId,
//                'story_id'      => $storyId,
//            ])
//            ->andFilterWhere([
//                'or',
//                ['>=', 'expire_time', time()],
//                ['expire_time' => 0]
//            ])
//            ->andFilterWhere([
//                'user_prize_status' => UserPrize::$normalUserPrizeStatus
//            ])
//            ->all();



        // 计算剩余数量
//        // 总数
//        $totalCt = $lotteryPrize->total_ct;
//        // 间隔内数量
//        $intervalRestCt = $intervalCt = $lotteryPrize->interval_ct;

        $userTotalPrizeCt = count($userPrize);
//        $restCt = $totalCt - $userTotalPrizeCt;



        $userPrizeArray = [];
        $userPrizeClassArray = [];
        if (!empty($userPrize)) {
            foreach ($userPrize as $up) {

                // 按时间计算
                $hourTag = Date('YmdH', $up->created_at);
                $dateTag = Date('Ymd', $up->created_at);
                $userPrizeArray[$hourTag][$up->prize_id] = !empty($userPrizeArray[$hourTag][$up->prize_id]) ?
                    $userPrizeArray[$hourTag][$up->prize_id] + 1 : 1;
                $userPrizeArray[$dateTag][$up->prize_id] = !empty($userPrizeArray[$dateTag][$up->prize_id]) ?
                    $userPrizeArray[$dateTag][$up->prize_id] + 1 : 1;
                $userPrizeArray[0][$up->prize_id] = !empty($userPrizeArray[0][$up->prize_id]) ?
                    $userPrizeArray[0][$up->prize_id] + 1 : 1;

                if (!empty($up->prize->prize_level)) {
                    $userPrizeClassArray[$hourTag][$up->prize->prize_level] = !empty($userPrizeClassArray[$hourTag][$up->prize->prize_level]) ?
                        $userPrizeClassArray[$hourTag][$up->prize->prize_level] + 1 : 1;
                    $userPrizeClassArray[$dateTag][$up->prize->prize_level] = !empty($userPrizeClassArray[$dateTag][$up->prize->prize_level]) ?
                        $userPrizeClassArray[$dateTag][$up->prize->prize_level] + 1 : 1;
                    $userPrizeClassArray[0][$up->prize->prize_level] = !empty($userPrizeClassArray[0][$up->prize->prize_level]) ?
                        $userPrizeClassArray[0][$up->prize->prize_level] + 1 : 1;
                }

            }
        }

        $prizePool = [];
        $rateTotal = $allRate = 10000;
        if (!empty($lotteryPrize)) {
            foreach ($lotteryPrize as $prize) {
                // 唯一中奖校验
                if ($prize->prize_method == LotteryPrize::PRIZE_METHOD_UNIQUE
                    && !empty($userPrizeArray[0][$prize->id])
                ) {
                    continue;
                }

                switch ($prize->interval_type) {
                    case LotteryPrize::INTERVAL_TYPE_HOUR:
                        $nowTimeTag = Date('YmdH');
                        break;
                    case LotteryPrize::INTERVAL_TYPE_DAY:
                    default:
                        $nowTimeTag = Date('Ymd');
                        break;
                }

                // 计算数量
                if ($prize->total_ct >= 0) {
                    if ($prize->total_ct <= $userTotalPrizeCt) {
                        continue;
                    }
                    $restCt = $prize->total_ct - $userTotalPrizeCt;
                } else {
                    $restCt = 9999999999999;
                }

                if ($prize->interval_ct >= 0) {
                    $intervalRestCt = !empty($userPrizeArray[$nowTimeTag][$prize->id]) ?
                        $prize->interval_ct - $userPrizeArray[$nowTimeTag][$prize->id] : $prize->interval_ct;
                } else {
                    $intervalRestCt = 9999999999999;
                }

                $optRestCt = $restCt < $intervalRestCt ? $restCt : $intervalRestCt;
                if ($optRestCt <= 0) {
                    continue;
                }

                // 解析中奖条件
                $prizeOptionJson = \common\helpers\Common::isJson($prize->prize_option) ?
                    json_decode($prize->prize_option, true) : [];

                if (!empty($prizeOptionJson['opt_formula'])) {
                    $optFormula = $prizeOptionJson['opt_formula'];
                    $optFormula = str_replace('{$opt_ct}', $optCt, $optFormula);

                    eval("\$optRet = $optFormula;");
                    if (!$optRet) {
                        continue;
                    }
                }


                $prizePool[] = [
                    'prize' => $prize,
                    'rest_ct' => $optRestCt,
                    'rateRange' => [
                        $rateTotal - $prize->rate,
                        $rateTotal
                    ],
                ];

                $rateTotal = $rateTotal - $prize->rate;

            }
        }

        if (!empty($prizePool)) {
            $lastPoolIdx = sizeof($prizePool) - 1;
            if ($prizePool[$lastPoolIdx]['rateRange'][0] > 0) {
                // 添加不中奖概率
                $prizePool[] = [
                    'prize' => 0,
                    'rest_ct' => 999999999,
                    'rateRange' => [
                        0,
                        $prizePool[$lastPoolIdx]['rateRange'][0]
                    ],
                ];

            }
//            $prizePool[$lastPoolIdx] = [
//                'prize' => $prizePool[$lastPoolIdx]['prize'],
//                'rest_ct' => $prizePool[$lastPoolIdx]['rest_ct'],
//                'rateRange' => [
//                    $prizePool[$lastPoolIdx]['rateRange'][0] > 0 ? 0 : $prizePool[$lastPoolIdx]['rateRange'][0],
//                    $prizePool[$lastPoolIdx]['rateRange'][1]
//                ],
//            ];
        }

        if (!empty($prizePool)) {
            $randRate = mt_rand(0, $allRate);
            $finalPrize = [];
            foreach ($prizePool as $pp) {
                if ($randRate >= $pp['rateRange'][0] && $randRate < $pp['rateRange'][1]) {
                    if (!empty($pp['prize'])) {
                        $finalPrize = $pp['prize'];
                        $isAward = 1;
                        $msg = '恭喜您，您得到 ' . $finalPrize->prize_name . ' 一份！';
                    } else {
                        $isAward = 0;
                        $msg = '很遗憾，您没有中奖！您继续答题，期待下次中奖！';
                    }
                    break;
                }
            }
        } else {
            $isAward = 0;
            $msg = '很遗憾，您没有中奖！您继续答题，期待下次中奖！';
        }

        $upnSession = !empty($sessionId) ? $sessionId : $channelId;
        $newUserPrize = null;

        $userLottery->ct = $userLottery->ct - 1;
        if (!empty($finalPrize)) {
            try {
                $newUserPrize = $this->add($userId, $sessionId, $channelId, $storyId,
                    $lotteryId, $userTotalPrizeCt, $finalPrize->id, $finalPrize->prize_type, 0,
                    UserPrize::USER_PRIZE_AWARD_METHOD_ONLINE);

                if ($userLottery->ct <= 0) {
                    $userLottery->lottery_status = UserLottery::USER_LOTTERY_STATUS_USED;
                }

            } catch (\Exception $e) {
//            $newUserPrize = null;
//            $msg = '您的操作出现异常，请您重试！';
                throw new \Exception('添加奖品失败', ErrorCode::USER_PRIZE_ADD_FAILED);
            }
        }
        $userLottery->save();

        return [
            'isAward' => $isAward,
            'msg'   => $msg,
            'newUserPrize' => $newUserPrize,
            'lottery' => $lottery,
//            'lotteryPrize' => $lotteryPrize,
//            'userPrize' => $userPrize,
//            'prizePool' => $prizePool,
            'finalPrize' => $finalPrize,
        ];

    }

    public function add($userId, $sessionId, $channelId, $storyId, $lotteryId, $userTotalPrizeCt, $prizeId, $prizeType, $expireTime = 0, $awardMethod = UserPrize::USER_PRIZE_AWARD_METHOD_ONLINE) {
        $userPrizeNo = \common\helpers\Common::generateNo('ZW'
            . $userId
            . \common\helpers\Common::generateFullNumber($sessionId, 2)
            . \common\helpers\Common::generateFullNumber($lotteryId, 2)
            . \common\helpers\Common::generateFullNumber($prizeId, 2)
            . \common\helpers\Common::generateFullNumber($prizeType, 2)
            , Date('YmdH'), \common\helpers\Common::generateFullNumber($userTotalPrizeCt + 1, 5), 10, 99
        );
        // 把奖品入库
        try {
            $newUserPrize = new UserPrize();
            $newUserPrize->user_prize_no = $userPrizeNo;
            $newUserPrize->user_id = $userId;
            $newUserPrize->session_id = $sessionId;
            $newUserPrize->channel_id = $channelId;
            $newUserPrize->story_id = $storyId;
            $newUserPrize->lottery_id = $lotteryId;
            $newUserPrize->prize_id = $prizeId;
            $newUserPrize->prize_type = $prizeType;
            $newUserPrize->award_method = $awardMethod;
            $newUserPrize->expire_time = $expireTime;
            $newUserPrize->user_prize_status = UserPrize::USER_PRIZE_STATUS_WAIT;
            $newUserPrize->save();
        } catch (\Exception $e) {
            throw new \Exception('添加奖品失败', ErrorCode::USER_PRIZE_ADD_FAILED);
        }

        return $newUserPrize;
    }

    public function generateLottery($userId, $storyId, $sessionId, $lotteryId, $channelId, $ct = 1) {
        $lottery = \common\models\Lottery::find()
            ->where([
                'id'    => $lotteryId
            ])
            ->one();

        if (empty($lottery)) {
            throw new NotFoundHttpException('Lottery not found');
        }

        $lotteryNo = \common\helpers\Common::generateNo('ZWT'
            . $userId
            . \common\helpers\Common::generateFullNumber($sessionId, 2)
            . \common\helpers\Common::generateFullNumber($lotteryId, 2)
            , Date('YmdH'), '', 1000, 9999
        );

        try {
            $userLottery = new UserLottery();
            $userLottery->user_id = $userId;
            $userLottery->lottery_no = $lotteryNo;
            $userLottery->lottery_id = $lotteryId;
            $userLottery->session_id = $sessionId;
            $userLottery->channel_id = $channelId;
            $userLottery->story_id = $storyId;
            $userLottery->lottery_status = UserLottery::USER_LOTTERY_STATUS_WAIT;
            $userLottery->ct = $ct;
            $userLottery->save();
        } catch (\Exception $e) {
            var_dump($e);
            throw new \Exception('添加抽奖机会失败', ErrorCode::USER_LOTTERY_ADD_FAILED);
        }

        return $userLottery;
    }

    public function getUserLottery($userId, $storyId, $sessionId, $lotteryId, $lotteryStatus = UserLottery::USER_LOTTERY_STATUS_WAIT) {
        $userLottery = UserLottery::find()
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
                'lottery_status' => $lotteryStatus
            ])
            ->all();

        return $userLottery;
    }

    public function getUserPrize($userId, $lotteryId, $sessionId, $storyId, $userPrizeStatus) {
        $userPrize = UserPrize::find()
            ->where([
                'user_id'   => $userId,
                'lottery_id'    => $lotteryId,
            ]);
        if (!empty($sessionId)) {
            $userPrize = $userPrize->andFilterWhere([
                'session_id'    => $sessionId
            ]);
        }
        if (!empty($storyId)) {
            $userPrize = $userPrize->andFilterWhere([
                'story_id'    => $storyId
            ]);
        }
        $userPrize = $userPrize->andFilterWhere([
                'or',
                ['>=', 'expire_time', time()],
                ['expire_time' => 0]
            ])
            ->andFilterWhere([
                'user_prize_status' => $userPrizeStatus
            ])
            ->all();

        return $userPrize;
    }

    public function getOneUserPrizeById($userPrizeId) {
        $userPrize = UserPrize::findOne($userPrizeId);

        return $userPrize;
    }


}