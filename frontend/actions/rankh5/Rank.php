<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\rankh5;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryRank;
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

class Rank extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $rankClass = !empty($_GET['rank_class']) ? $_GET['rank_class'] : 0;

        $scoreSort = !empty($_GET['score_sort']) ? $_GET['score_sort'] : SORT_DESC;
        $score2Sort = !empty($_GET['score2_sort']) ? $_GET['score2_sort'] : SORT_DESC;
        $topNum = !empty($_GET['top_num']) ? $_GET['top_num'] : 10;

        if (!empty($storyId)) {
            $story = Story::find()
                ->where(['id' => $storyId])
                ->one();
        }

        $storyName = !empty($story->title) ? $story->title : '未知故事';

        $rankConfig = !empty(StoryRank::$storyRankCategories[$storyId][$rankClass])
            ? StoryRank::$storyRankCategories[$storyId][$rankClass]
            : [
                'score' => '成绩',
                'score2' => '副成绩',
            ];

        try {
            $rankRet = StoryRank::find()
                ->where([
                    'story_id' => $storyId,
                    'rank_class' => $rankClass,
                ])
                ->orderBy([
                    'score' => $scoreSort,
                    'score2' => $score2Sort,
                ])
                ->all();

            $rank = 0;
            $rankList = [];
            if (!empty($rankRet)) {
                $hasFound = false;
                $topNum = $topNum;
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

        return $this->controller->render('rank', [
            'model'            => $rankRet,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'rankList'  => $rankList,
            'rankConfig' => $rankConfig,
        ]);

    }
}