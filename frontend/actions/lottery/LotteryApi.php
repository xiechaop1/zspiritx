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
use common\models\Qa;
use common\models\SessionQa;
use common\models\StoryStages;
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
            if (empty($this->_storyId)) {
                throw new \Exception('剧本不存在', ErrorCode::STORY_NOT_FOUND);
            }

            switch ($this->action) {
                case 'award':
                    $ret = $this->award();
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