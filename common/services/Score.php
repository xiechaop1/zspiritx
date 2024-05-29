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
use common\models\Qa;
use common\models\Session;
use common\models\UserKnowledge;
use common\models\UserQa;
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

    public function computeWithQa($userId, $storyId, $sessionId, $qaId, $qaScore = 0, $beginTs = 0) {
        $userQa = UserQa::findOne(['user_id' => $userId, 'session_id' => $sessionId, 'qa_id' => $qaId]);

        $score = 0;
        if (empty($qaScore)) {
            $qa = Qa::findOne($qaId);
            if (!empty($qa)) {
                $qaScore = $qa['score'];
            } else {
                $qaScore = 0;
            }
        }

        $x = 1.00;
        $addition = 0;

        $nowTime = time();
        if (!empty($beginTs)) {
            $timeInterval = intval($nowTime - $beginTs);
            if ($timeInterval < 30) {
                $addition = $qaScore;
            } elseif ($timeInterval >= 30 && $timeInterval < 90) {
                $addition = $qaScore * 0.5;
            } else {
                $addition = 0;
            }
        }

        if (!empty($userQa)) {

            // 超过半天重新加金币
            if (time() - $userQa['created_at'] > 43200) {
                $score = $qaScore;
            } else {
                if ($userQa['is_right'] == 1) {
                    $score = 0;
                    $addition = 0;
                } else {
                    $score = $qaScore;
                    $x *= 0.5;
                }
            }
        } else {
            $score = $qaScore;
        }

        $score = $score * $x + $addition;

        return [
            'score' => $score,
            'x' => $x,
            'addition' => $addition,
        ];
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