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
use common\models\Session;
use common\models\UserKnowledge;
use common\models\UserScore;
use yii\base\Component;
use yii;

class Score extends Component
{
    public function get($userId, $storyId, $sessionId) {
        $userScore = UserScore::find()
            ->where([
                'user_id' => $userId,
                'story_id' => $storyId,
                'session_id' => $sessionId,
            ])->one();

        return $userScore;
    }

    public function getList($storyId, $sessionId) {
        $userScores = UserScore::find()
            ->where([
                'story_id'  => $storyId,
                'session_id' => $sessionId,
            ])
            ->orderBy(['score' => SORT_DESC])
            ->all();

        return $userScores;
    }

    public function add($userId, $storyId, $sessionId, $sessionStageId, $score = 0, $teamId = 0) {

        if (empty($sessionId)) {
            throw new \Exception('没有找到场次', ErrorCode::SESSION_NOT_FOUND);
        }

        $sessionInfo = Session::findOne($sessionId);

        if (empty($sessionInfo)
            || ($sessionInfo->session_status == Session::SESSION_STATUS_CANCEL
                or $sessionInfo->session_status == Session::SESSION_STATUS_FINISH
            )
        ) {
            throw new \Exception('场次不存在', ErrorCode::SESSION_NOT_FOUND);
        }

        $userScore = UserScore::find()
            ->where([
                'user_id' => $userId,
                'story_id' => $storyId,
                'session_id' => $sessionId,
                'team_id'   => $teamId,
            ])->one();

        $userTotalScore = UserScore::find()
            ->where([
                'user_id' => $userId,
                'story_id' => 0,
            ])->one();

        try {

            if (!empty($userScore)) {
                $userScore->score = $userScore->score + $score;
            } else {
                $userScore = new UserScore();
                $userScore->user_id = $userId;
                $userScore->story_id = $storyId;
                $userScore->session_id = $sessionId;
                $userScore->team_id = $teamId;
                $userScore->score = $score;
            }
            $userScore->save();

            if (!empty($userTotalScore)) {
                $userTotalScore->score = $userTotalScore->score + $score;
            } else {
                $userTotalScore = new UserScore();
                $userTotalScore->user_id = $userId;
                $userTotalScore->story_id = 0;
                $userTotalScore->session_id = 0;
                $userTotalScore->team_id = 0;
                $userTotalScore->score = $score;
            }
            $userTotalScore->save();

            if ($score >= 0) {
                $act = '恭喜！获得';
            } else {
                $act = '减少';
            }

            $scoreAbs = abs($score);

//            Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, $act . $scoreAbs . '金币！', Actions::ACTION_TYPE_MSG);

        } catch (\Exception $e) {
            throw new \Exception('完成进程失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
        }


        return $userScore;
    }


}