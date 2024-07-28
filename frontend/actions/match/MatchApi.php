<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\match;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\helpers\Attachment;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Qa;
use common\models\SessionQa;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryStages;
use common\models\UserExtends;
use common\models\UserQa;
use common\models\User;
//use liyifei\base\actions\ApiAction;
use common\models\UserList;
use common\models\UserScore;
use common\models\UserStory;
use frontend\actions\ApiAction;
use yii;

class MatchApi extends ApiAction
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

                case 'update_match':
                    $ret = $this->updateMatch();
                    break;
                case 'add_knock_player':
                    $ret = $this->addKnockPlayer();
                    break;
                case 'update_knock_players':
                    $ret = $this->updateKnockPlayers();
                    break;
                case 'get_knockout_status':
                    $ret = $this->getKnockoutStatus();
                    break;
                case 'get_knockout_players_in_match':
                    $ret = $this->getKnockoutPlayersInMatch();
                    break;
                case 'get_suggestion_from_subject':
                    $ret = $this->getSuggestionFromSubject();
                    break;
                case 'get_subjects':
                    $ret = $this->getSubjects();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
//            var_dump($e);exit;
            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }

        return $this->success($ret);
    }

    public function getSubjects() {
        $level = !empty($this->_get['level']) ? $this->_get['level'] : 0;
        $matchClass = !empty($this->_get['match_class']) ? $this->_get['match_class'] : 0;
        $ct = !empty($this->_get['ct']) ? $this->_get['ct'] : 10;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        return Yii::$app->qas->generateTotalSubjects($level, $matchClass, $ct, $userId);
    }

    public function getSuggestionFromSubject() {
        $topic = !empty($this->_get['topic']) ? $this->_get['topic'] : '';
        $level = !empty($this->_get['level']) ? $this->_get['level'] : 0;
        $matchClass = !empty($this->_get['match_class']) ? $this->_get['match_class'] : 0;

        $suggestion = Yii::$app->doubao->generateSuggestionFromSubject($topic, $level, $matchClass);

        $ret = [
            'suggestion' => $suggestion,
            'size' => mb_strlen($suggestion) > 20 ? 35 : 40,
        ];
//        var_dump($ret);exit;

        return $ret;
    }

    public function addKnockPlayer() {
        $matchId = !empty($this->_get['match_id']) ? $this->_get['match_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $userExtends = UserExtends::find()
            ->where([
                'user_id' => $userId,
            ])
            ->one();

        $storyMatch = StoryMatch::find()
            ->where([
                'id' => $matchId,
                'story_match_status' => [
                    StoryMatch::STORY_MATCH_STATUS_MATCHING,
                    StoryMatch::STORY_MATCH_STATUS_PLAYING,
                ]
            ])
            ->one();

        $fee = 2000;
        if (!empty($storyMatch->match_detail)) {
            $matchDetail = json_decode($storyMatch->match_detail, true);
            $fee = !empty($matchDetail['fee']) ? $matchDetail['fee'] : $fee;
        }

        $userScore = Yii::$app->score->get($userId, $storyId, 0);
        if ($userScore->score < $fee) {
            throw new \Exception('金币不足，不能报名', ErrorCode::STORY_MATCH_JOIN_FAILED);
        } else {
            $userScore = Yii::$app->score->add($userId, $storyId, 0, 0, -$fee);
        }

        $bonusJson = $storyMatch->bonus;
        $bonus = !empty($bonusJson) ? json_decode($bonusJson, true) : [];
        $bonus['score'] = !empty($bonus['score']) ? $bonus['score'] + $fee : $fee;
        $storyMatch->bonus = json_encode($bonus, JSON_UNESCAPED_UNICODE);
        $storyMatch->save();

        $storyMatchPlayer = StoryMatchPlayer::find()
            ->where([
                'match_id' => $matchId,
                'user_id' => $userId,
                'match_player_status' => [
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE,
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
                ]
            ])
            ->one();

        if (empty($storyMatchPlayer)) {
            $playerProp = [
                'grade' => !empty($userExtends->grade) ? $userExtends->grade : 1,
                'level' => !empty($userExtends->level) ? $userExtends->level : 1,
            ];
            $storyMatchPlayer = new StoryMatchPlayer();
            $storyMatchPlayer->user_id = $userId;
            $storyMatchPlayer->team_id = 1;
            $storyMatchPlayer->match_id = $matchId;
            $storyMatchPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING;
            $storyMatchPlayer->m_user_model_prop = json_encode($playerProp, JSON_UNESCAPED_UNICODE);
            $storyMatchPlayer->save();
        }

        return $storyMatchPlayer;
    }

    public function updateKnockPlayers() {
        $matchId = !empty($this->_get['match_id']) ? $this->_get['match_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $knockoutStatus = !empty($this->_get['knockout_status']) ? $this->_get['knockout_status'] : 0;
        $player = StoryMatchPlayer::find()
            ->where([
                'match_id' => $matchId,
                'user_id' => $userId,
                'story_match_player_status' => StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
            ])
            ->one();

        if (!empty($player)) {
            $player->match_player_status = $knockoutStatus;
            $player->save();
        }

        return $player;
    }

    public function getKnockoutPlayersInMatch() {
        $matchId = !empty($this->_get['match_id']) ? $this->_get['match_id'] : 0;
        $players = StoryMatchPlayer::find()
            ->where([
                'match_id' => $matchId,
                'match_player_status' => StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
            ])
            ->all();

        $playerIds = [];
        $playersCt = 0;
        if (!empty($players)) {
            foreach ($players as $player) {
                $playerIds[] = $player->id;
                $playersCt++;
            }
        }

        return [
            'players' => $players,
            'playerIds' => $playerIds,
            'players_ct' => $playersCt,
        ];
    }

    public function getKnockoutStatus() {
        $status = 'matching';
        $matchId = !empty($this->_get['match_id']) ? $this->_get['match_id'] : 0;
        $matchType = !empty($this->_get['match_type']) ? $this->_get['match_type'] : StoryMatch::MATCH_TYPE_KNOCKOUT;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

        $storyMatch = StoryMatch::find()
            ->where([
                'id' => $matchId,
                'match_type' => $matchType,
                'story_match_status' => [
                    StoryMatch::STORY_MATCH_STATUS_MATCHING,
                    StoryMatch::STORY_MATCH_STATUS_PLAYING,
                    StoryMatch::STORY_MATCH_STATUS_END
                ],
            ])
            ->one();

        if (empty($storyMatch)) {
            throw new \Exception('对战不存在', ErrorCode::STORY_MATCH_NOT_READY);
        }

        if (!empty($storyMatch)) {
            if ($storyMatch->story_match_status == StoryMatch::STORY_MATCH_STATUS_PLAYING) {
                $status = 'playing';
            } elseif ($storyMatch->story_match_status == StoryMatch::STORY_MATCH_STATUS_MATCHING) {

                if ($storyMatch->join_expire_time <= time()
//                    && 1 != 1
                ) {
                    $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_PLAYING;
                    $storyMatch->save();

                    if (!empty($storyMatch->players)) {
                        $playerCt = 0;
                        foreach ($storyMatch->players as $player) {
                            $player->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING;
                            $player->save();
                            $playerCt++;
                        }

                        // Todo： 暂时去掉补充机器人
//                        if ($playerCt < $storyMatch->max_players_ct) {
//                            for ($i=0; $i<$storyMatch->max_players_ct - $playerCt; $i++) {
//                                $player = new StoryMatchPlayer();
//                                $player->user_id = 0;
//
//                                $player->match_id = $matchId;
//                                $player->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING;
//                                $player->save();
//                            }
//                        }
                    }

                    $status = 'playing';
                } else {
                    $playersCt = StoryMatchPlayer::find()
                        ->where([
                            'match_id' => $matchId,
                            'match_player_status' => [
                                StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
                                StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
                            ],
                        ])
                        ->count();

                    if ($playersCt >= $storyMatch->max_players_ct) {
                        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_PLAYING;
                        $storyMatch->save();
                        $status = 'playing';

                        if (!empty($storyMatch->players)) {
                            foreach ($storyMatch->players as $player) {
                                $player->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING;
                                $player->save();
                            }
                        }
                    }
                }

            } else {
                // 已经结束了
                $status = 'end';
            }
        }

        $playersData = StoryMatchPlayer::find()
            ->where([
                'match_id' => $matchId,
                'match_player_status' => [
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
                ],
            ])
//            ->asArray()
            ->all();

        $players = [];
        $myPlayer = [];
        if (!empty($playersData)) {
            foreach ($playersData as $player) {
                if ($player->user_id == $userId) {
                    $myPlayer = $player->toArray();
                }
                $tmp = $player->toArray();
                if (!empty($player->user)) {
                    $tmp['user'] = $player->user->toArray();
                } else {
                    $tmp['user']['user_name'] = 'AI-' . rand(1000,9999);
                }
                if (!empty($tmp['user']['avatar'])) {
                    $tmp['user']['avatar'] = Attachment::completeUrl($tmp['user']['avatar'], 60);
                } else {
                    $tmp['user']['avatar'] = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
                }
                $players[] = $tmp;
            }
        }

        return [
            'status' => $status,
            'match' => $storyMatch,
            'my_player' => $myPlayer,
            'players' => $players,
            'players_ct' => sizeof($players),
        ];

    }

    public function updateMatch() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $answer = !empty($this->_get['answer']) ? $this->_get['answer'] : '';
        $qaId = !empty($this->_get['qa_id']) ? $this->_get['qa_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $beginTs = !empty($this->_get['begin_ts']) ? $this->_get['begin_ts'] : 0;

        $matchId = !empty($this->_get['match_id']) ? $this->_get['match_id'] : 0;
        $score = !empty($this->_get['score']) ? $this->_get['score'] : 0;
        $subjectCt = !empty($this->_get['subjct']) ? $this->_get['subjct'] : 0;
        $rightCt = !empty($this->_get['right_ct']) ? $this->_get['right_ct'] : 0;
        $wrongCt = !empty($this->_get['wrong_ct']) ? $this->_get['wrong_ct'] : 0;

        $storyMatch = StoryMatch::find()->where(['id' => $matchId])->one();


        try {
            $transaction = Yii::$app->db->beginTransaction();

            Yii::$app->score->add($userId, $storyId, $sessionId, 0, $score);

            if ($storyMatch->match_type != StoryMatch::MATCH_TYPE_KNOCKOUT) {
                $userExtends = Yii::$app->userService->updateUserLevelWithRight($userId, $subjectCt, $rightCt);
//                if ($subjectCt > 0) {
//                    if (($rightCt / $subjectCt) > 0.8) {
//                        $addLevel = 1;
//                        Yii::$app->userService->updateUserLevel($userId, $addLevel);
//                    } elseif (($rightCt / $subjectCt) < 0.4) {
//                        $addLevel = -1;
//                        Yii::$app->userService->updateUserLevel($userId, $addLevel);
//                    }
//                }
            }


            $matchDetail = [
                'qa_id' => $qaId,
                'answer' => $answer,
                'begin_ts' => $beginTs,
                'end_ts' => time(),
                'subject_ct' => $subjectCt,
                'score' => $score,
            ];

            if ($storyMatch->match_type == StoryMatch::MATCH_TYPE_KNOCKOUT) {
                $myPlayer = StoryMatchPlayer::find()
                    ->where([
                        'match_id' => $matchId,
                        'user_id' => $userId,
                    ])
                    ->one();
//                var_dump($answer);
                if ($answer == 1) {
                    $myPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_END;
                    $myPlayer->save();
//                    echo '1';
                } else {
                    $myPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_LOST;
                    $myPlayer->save();
//                    echo '2';
                }

                $players = StoryMatchPlayer::find()
                    ->where([
                        'match_id' => $matchId,
                        'match_player_status' => [
//                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_END,
                        ],
                    ])
                    ->all();
                $playingPlayersCt = 0;
                $endPlayersCt = 0;
                $endPlayers = [];
                if (!empty($players)) {
                    foreach ($players as $pla) {
                        if ($pla->match_player_status == StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING
                        ) {
                            $playingPlayersCt++;
                        } elseif ($pla->match_player_status == StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_END) {
                            $endPlayersCt++;
                            $endPlayers[] = $pla;
                        }
                    }
                }
                if ($playingPlayersCt == 0) {
//                    $storyMatch->match_detail = json_encode($matchDetail, true);
                    $storyMatch->score = $score;
                    $storyMatch->score2 = $subjectCt;
                    $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
                    $storyMatch->save();

                    $bonusJson = $storyMatch->bonus;
                    $bonus = json_decode($bonusJson, true);
                    $score = !empty($bonus['score']) ? $bonus['score'] : 0;

                    if ($endPlayersCt > 0) {
                        $eachScore = intval($score / $endPlayersCt);
                    }

                    if (!empty($endPlayers)) {
                        foreach ($endPlayers as $endPlayer) {
                            Yii::$app->score->add($endPlayer->user_id, $storyId, 0, 0, $eachScore);
                        }
                    }
                }

            } else {
                if ($answer == 1) {
                    if (!empty($storyMatch->players)) {
                        foreach ($storyMatch->players as $player) {
                            $player->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_END;
//                        $player->save();
                        }
                    }

                    $storyMatch->match_detail = json_encode($matchDetail, true);
                    $storyMatch->score = $score;
                    $storyMatch->score2 = $subjectCt;
                    $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
//                $storyMatch->save();
                } else {
                    $storyMatch->match_detail = json_encode($matchDetail, true);
                    $storyMatch->score = $score;
                    $storyMatch->score2 = $subjectCt;

//                $storyMatch->save();
                }
            }

            $transaction->commit();

        } catch (\Exception $e) {
            var_dump($e);
            $transaction->rollBack();
            throw new \Exception('添加用户问答失败', ErrorCode::QA_SAVE_FAILED);
        }

        $scoreRet = [
            'score' => $score,
        ];

        $ret['score'] = $scoreRet;

        return $ret;
    }

}