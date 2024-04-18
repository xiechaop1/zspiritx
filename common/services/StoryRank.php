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
use common\models\Story;
use common\models\UserKnowledge;
use common\models\UserLottery;
use common\models\UserPrize;
use common\models\UserQa;
use common\models\UserScore;
use yii\base\Component;
use yii;
use yii\web\NotFoundHttpException;

class StoryRank extends Component
{
    public function addRank($userId, $storyId, $sessionId,
                            $rankClass, $score, $scoreSort = \common\models\StoryRank::STORY_RANK_SORT_DESC,
                            $score2 = 0, $score2Sort = \common\models\StoryRank::STORY_RANK_SORT_DESC ,
                            $storyModelId = 0, $storyModelDetailId = 0, $userModelId = 0) {
        try {
            $storyRank = \common\models\StoryRank::find()
                ->where([
                    'user_id' => $userId,
                    'story_id' => $storyId,
                    'session_id' => $sessionId,
                    'rank_class' => $rankClass,
                ])
                ->one();

            if (empty($storyRank)) {

                $storyRank = new \common\models\StoryRank();
                $storyRank->user_id = $userId;
                $storyRank->story_id = $storyId;
                $storyRank->session_id = $sessionId;
                $storyRank->score = $score;
                $storyRank->score2 = $score2;
                $storyRank->story_model_id = $storyModelId;
                $storyRank->story_model_detail_id = $storyModelDetailId;
                $storyRank->user_model_id = $userModelId;
                $storyRank->rank_class = $rankClass;

            } else {
                $needChange = false;
                if ($scoreSort == \common\models\StoryRank::STORY_RANK_SORT_ASC) {
                    if ($score < $storyRank->score) {
                        $storyRank->score = $score;
                        $needChange = true;
                    }
                } else {
                    if ($score > $storyRank->score) {
                        $storyRank->score = $score;
                        $needChange = true;
                    }
                }

                if (!empty($score2)) {
                    if ($score2Sort == \common\models\StoryRank::STORY_RANK_SORT_ASC) {
                        if ($score2 < $storyRank->score2) {
                            $storyRank->score2 = $score;
                            $needChange = true;
                        }
                    } else {
                        if ($score2 > $storyRank->score2) {
                            $storyRank->score2 = $score;
                            $needChange = true;
                        }
                    }
                }

                if ($needChange) {
                    $storyRank->story_model_id = $storyModelId;
                    $storyRank->story_model_detail_id = $storyModelDetailId;
                    $storyRank->user_model_id = $userModelId;
                }

            }
            $storyRank->save();
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            throw $e;
        }
    }


}