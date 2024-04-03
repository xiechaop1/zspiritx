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
use common\models\UserLottery;
use common\models\UserPrize;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class CashPrize extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $lotteryId = !empty($_GET['lottery_id']) ? $_GET['lottery_id'] : 0;
        $userPrizeId = !empty($_GET['user_prize_id']) ? $_GET['user_prize_id'] : 0;

//        $optCt = !empty($_GET['opt_ct']) ? $_GET['opt_ct'] : 0;

//        $storyModelId = !empty($_GET['story_model_id']) ? $_GET['story_model_id'] : 0;


        $userPrizes = Yii::$app->lottery->getUserPrize($userId, $lotteryId, $sessionId, $storyId,
            [UserPrize::USER_PRIZE_STATUS_PREPARING, UserPrize::USER_PRIZE_STATUS_WAIT, UserPrize::USER_PRIZE_STATUS_RECEIVED, UserPrize::USER_PRIZE_STATUS_CANCEL]);

        $oneUserPrize = [];
        $prevUserPrize = [];
        $nextUserPrize = [];
        if (!empty($userPrizes)) {
            $got = 0;
            foreach ($userPrizes as $userPrize) {
                if ($got == 1) {
                    $nextUserPrize = $userPrize;
                    break;
                }
                if (!empty($userPrizeId)) {
                    if ($userPrize->id == $userPrizeId) {
                        $oneUserPrize = $userPrize;
                        $got = 1;
                    }
                } else {
                    $oneUserPrize = $userPrize;
                    $got = 1;
                }
                if ($got == 0) {
                    $prevUserPrize = $userPrize;
                }
            }
        }


//        $ret = Yii::$app->lottery->run($userId, $storyId, $sessionId, $lotteryId, $channelId, $optCt);
//
//        $msg = $ret['msg'];
//        $newUserPrize = $ret['newUserPrize'];
//        $lottery = $ret['lottery'];
//        $lotteryPrize = $ret['lotteryPrize'];
//        $userPrize = $ret['userPrize'];
//        $prizePool = $ret['prizePool'];
//        $finalPrize = $ret['finalPrize'];



        return $this->controller->render('cash_prize', [
            'params'        => $_GET,
            'userId'        => $userId,
            'userLotteryId' => !empty($userLottery->id) ? $userLottery->id : 0,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'lotteryId'     => $lotteryId,
            'channelId'     => $channelId,
            'userPrize'     => $oneUserPrize,
            'prevUserPrize' => $prevUserPrize,
            'nextUserPrize' => $nextUserPrize,
//            'storyModelId'  => $storyModelId,

//            'prize'         => $finalPrize,
//            'lotteryPrize'    => $lotteryPrize,
//            'lottery'       => $lottery,
//            'userPrize'     => $userPrize,
//            'newUserPrize'  => $newUserPrize,
//            'msg'           => $msg,
        ]);
    }
}