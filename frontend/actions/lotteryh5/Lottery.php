<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\lotteryh5;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\User;
use common\models\UserPrize;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Lottery extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $lotteryId = !empty($_GET['lottery_id']) ? $_GET['lottery_id'] : 0;

        $optCt = !empty($_GET['opt_ct']) ? $_GET['opt_ct'] : 0;

        $lottery = \common\models\Lottery::find()
            ->where([
                'id'    => $lotteryId
            ])
            ->one();

        if (empty($lottery)) {
            throw new NotFoundHttpException('Lottery not found');
        }

        $lotteryPrize = LotteryPrize::find()
            ->where([
                'lottery_id'    => $lotteryId,
            ])
            ->orderBy([
                'prize_level'   => SORT_DESC
            ])
            ->all();

        $userPrize = UserPrize::find()
            ->where([
                'user_id'   => $userId,
                'lottery_id'    => $lotteryId,
                'session_id'    => $sessionId,
                'story_id'      => $storyId,
            ])
            ->andFilterWhere([
                'or',
                ['>=', 'expire_time', time()],
                ['expire_time' => 0]
            ])
            ->andFilterWhere([
                'user_prize_status' => UserPrize::$normalUserPrizeStatus
            ])
            ->all();

        // 计算剩余数量
//        // 总数
//        $totalCt = $lotteryPrize->total_ct;
//        // 间隔内数量
//        $intervalRestCt = $intervalCt = $lotteryPrize->interval_ct;

        $userTotalPrizeCt = count($userPrize);
//        $restCt = $totalCt - $userTotalPrizeCt;



        $userPrizeArray = [];
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
            $prizePool[$lastPoolIdx] = [
                'prize' => $prizePool[$lastPoolIdx]['prize'],
                'rest_ct' => $prizePool[$lastPoolIdx]['rest_ct'],
                'rateRange' => [
                    $prizePool[$lastPoolIdx]['rateRange'][0] > 0 ? 0 : $prizePool[$lastPoolIdx]['rateRange'][0],
                    $prizePool[$lastPoolIdx]['rateRange'][1]
                ],
            ];
            $randRate = mt_rand(0, $allRate);
            $finalPrize = [];
            foreach ($prizePool as $pp) {
                if ($randRate >= $pp['rateRange'][0] && $randRate < $pp['rateRange'][1]) {
                    $finalPrize = $pp['prize'];
                    break;
                }
            }
            $msg = '恭喜您，您得到 ' . $finalPrize->prize_name . ' 一份！';
        } else {
            $msg = '很遗憾，您没有中奖，再接再厉！';
        }

        $upnSession = !empty($sessionId) ? $sessionId : $channelId;

        $userPrizeNo = \common\helpers\Common::generateNo('ZW'
            . $userId
            . \common\helpers\Common::generateFullNumber($upnSession, 2)
//            . \common\helpers\Common::generateFullNumber($lotteryId, 2)
            . \common\helpers\Common::generateFullNumber($finalPrize->id, 2)
            . \common\helpers\Common::generateFullNumber($finalPrize->prize_type, 2)
            , $nowTimeTag, \common\helpers\Common::generateFullNumber($userTotalPrizeCt, 5), 10, 99
        );
        // 把奖品入库
        $newUserPrize = new UserPrize();
        $newUserPrize->user_prize_no = $userPrizeNo;
        $newUserPrize->user_id = $userId;
        $newUserPrize->session_id = $sessionId;
        $newUserPrize->channel_id = $channelId;
        $newUserPrize->story_id = $storyId;
        $newUserPrize->lottery_id = $lotteryId;
        $newUserPrize->prize_id = $finalPrize->id;
        $newUserPrize->prize_type = $finalPrize->prize_type;
        $newUserPrize->award_method = UserPrize::USER_PRIZE_AWARD_METHOD_ONLINE;
        $newUserPrize->expire_time = 0;
        $newUserPrize->user_prize_status = UserPrize::USER_PRIZE_STATUS_WAIT;
        $newUserPrize->save();


//        $qaOne = $qaOne->toArray();
//        $qaOne['selected_json'] = \common\helpers\Common::isJson($qaOne['selected']) ? json_decode($qaOne['selected'], true) : $qaOne['selected'];
//        $qaOne['attachment'] = \common\helpers\Attachment::completeUrl($qaOne['attachment'], true);
        var_dump($randRate);
var_dump($msg);
var_dump($prizePool);
var_dump($newUserPrize);
exit;

        return $this->controller->render('lottery', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'prize'         => $finalPrize,
            'storyId'       => $storyId,
            'lotteryPrize'    => $lotteryPrize,
            'lottery'       => $lottery,
            'userPrize'     => $userPrize,
            'newUserPrize'  => $newUserPrize,
            'msg'           => $msg,
        ]);
    }
}