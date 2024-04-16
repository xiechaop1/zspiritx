<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\matchh5;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\StoryMatch;
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

class Rankofmatch extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $matchName = '中国勒芒';

        try {
            $rankRet = StoryMatch::find()
                ->where([
                    'story_id' => $storyId,
//                'user_id' => $userId,
//                'session_id' => $sessionId,
                    'match_name' => $matchName,
//                    'story_match_status' => StoryMatch::STORY_MATCH_STATUS_END
                ])
//                ->andFilterWhere([
//                    '>=', 'score', $ct
//                ])
                ->orderBy([
                    'score' => SORT_DESC,
                    'score2' => SORT_ASC,
                ])
                ->all();

            $rank = 0;
            $rankList = [];
            if (!empty($rankRet)) {
                $hasFound = false;
                $topNum = 10;
                foreach ($rankRet as $r) {

                    $rank++;
                    if ($rank <= $topNum
                        || $r->user_id == $userId
                    ) {
                        $rankList[$rank] = $r;
                        if ($r->user_id == $userId) {
                            $hasFound = true;
                        }
                    }

                    if ($rank > $topNum && $hasFound) {
                        break;
                    }

                }
            } else {
//                throw new NotFoundHttpException('未找到比赛记录');
            }

        } catch (\Exception $e) {
            throw $e;
        }

        return $this->controller->render('rankofmatch', [
            'model'            => $rankRet,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'rankList'  => $rankList,
        ]);

    }
}