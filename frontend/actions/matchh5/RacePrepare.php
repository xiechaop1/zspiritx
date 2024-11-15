<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\matchh5;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\definitions\Subject;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\helpers\Model;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryModels;
use common\models\User;
use common\models\UserExtends;
use common\models\UserLottery;
use common\models\UserModelLoc;
use common\models\UserModels;
use common\models\UserPrize;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class RacePrepare extends Action
{

    // 同时参与的人数
    const MAX_PLAYER_CT = 2;
    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $matchType = !empty($_GET['match_type']) ? $_GET['match_type'] : StoryMatch::MATCH_TYPE_RACE;
        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

        $matchClass = !empty($_GET['match_class']) ? $_GET['match_class'] : 0;
        $matchName = !empty($_GET['match_name']) ? $_GET['match_name'] : '';
        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;
        $poiId = !empty($_GET['poi_id']) ? $_GET['poi_id'] : 0;
        $rivalUserModelIds = !empty($_GET['rival_user_model_ids']) ? $_GET['rival_user_model_ids'] : 0;
        $rivalUserId = !empty($_GET['rival_user_id']) ? $_GET['rival_user_id'] : 0;
        $rivalStoryModelIds = !empty($_GET['rival_story_model_ids']) ? $_GET['rival_story_model_ids'] : 0;
        $rivalLocationId = !empty($_GET['rival_location_id']) ? $_GET['rival_location_id'] : 0;

        $storyModelId = !empty($_GET['story_model_id']) ? $_GET['story_model_id'] : 0;
        $storyModelDetailId = !empty($_GET['story_model_detail_id']) ? $_GET['story_model_detail_id'] : 0;
//        $rivalStoryModelDetailId = !empty($_GET['rival_story_model_detail_id']) ? $_GET['rival_story_model_detail_id'] : 0;
        $userModelIds = !empty($_GET['user_model_ids']) ? $_GET['user_model_ids'] : 0;
        $joinExpireTime = !empty($_GET['join_expire_time']) ? $_GET['join_expire_time'] : 1800; // (s)
        $isAi = !empty($_GET['is_ai']) ? $_GET['is_ai'] : 0;
        $aiLevel = !empty($_GET['ai_level']) ? $_GET['ai_level'] : 1;

        if (empty($isAi)) {
            if (empty($userId) || empty($sessionId) || empty($storyId)) {
//            throw new Exception('参数错误', ErrorCode::PARAMS_ERROR);
                return $this->renderErr('参数错误');
            }

            $userInfo = User::find()
                ->where(['id' => $userId])
                ->one();

            $userExtends = UserExtends::find()
                ->where(['user_id' => $userId])
                ->one();

            $userLevel = !empty($userExtends->level) ? $userExtends->level : 1;

            if (empty($matchId) && empty($matchName)) {
                $matchName = date('Y-m-d H:i:s') . ' ' . $userInfo->user_name . '发起对战';
            }

            $avatar = $userInfo->avatar;
            if (empty($avatar)) {
                $avatar = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
            }

            if (!empty($matchId)) {
                $storyMatch = StoryMatch::find()
                    ->where([
                        'id' => $matchId,
                        'match_type' => StoryMatch::MATCH_TYPE_RACE,
                        'user_id' => $userId,
                        'session_id' => $sessionId,
                        'story_id' => $storyId,
                        'story_match_status' => [
                            StoryMatch::STORY_MATCH_STATUS_PREPARE,
                            StoryMatch::STORY_MATCH_STATUS_MATCHING,
                            StoryMatch::STORY_MATCH_STATUS_PLAYING
                        ],
                    ])
                    ->one();

                if (empty($storyMatch)) {
    //                throw new Exception('对战不存在', ErrorCode::STORY_MATCH_NOT_READY);
                    return $this->renderErr('对战不存在');
                }
                $matchId = $storyMatch->id;
            } else {

                $newPlayerTeam = 0;

                // 寻找自己未完结的比赛
                $storyMatchPlayerFound = StoryMatchPlayer::find()
                    ->select('match_id')
                    ->where([
                        'user_id' => $userId,
                        'match_player_status' => [
                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE,
                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
                            StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
                        ]
                    ]);
    //                ->all();
    //                ->createCommand()
    //                ->getRawSql();
    //                ->orderBy([
    //                    'id' => SORT_DESC,
    //                ])
    //                ->one();

    //            if (!empty($storyMatchPlayerFound)) {
    //                $matchId = $storyMatchPlayerFound->match_id;
    //                $storyMatch = StoryMatch::find()
    //                    ->where([
    //                        'id' => $matchId,
    //                        'story_match_status' => [
    //                            StoryMatch::STORY_MATCH_STATUS_PREPARE,
    //                            StoryMatch::STORY_MATCH_STATUS_MATCHING,
    //                            StoryMatch::STORY_MATCH_STATUS_PLAYING
    //                        ]
    //                    ])
    //                    ->one();
    //            }

                $storyMatch = StoryMatch::find()
                    ->where([
                        'match_type' => StoryMatch::MATCH_TYPE_RACE,
                        'match_class' => $matchClass,
                        'story_id' => $storyId,
                        'story_match_status' => [
                            StoryMatch::STORY_MATCH_STATUS_PREPARE,
                            StoryMatch::STORY_MATCH_STATUS_MATCHING,
                            StoryMatch::STORY_MATCH_STATUS_PLAYING
                        ],
                    ])
                    ->andFilterWhere([
                        'IN', 'id', $storyMatchPlayerFound
                    ])
                    ->one();

                if (!empty($storyMatch)) {
                    $matchId = $storyMatch->id;
                }

                if (empty($storyMatch)) {

                    // 寻找可以匹配的比赛
                    // 也就是参与的人数<2的比赛
                    $storyMatchPlayerCt = StoryMatchPlayer::find()
                        ->select('match_id')
                        ->where([
                            'match_player_status' => [
                                StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE,
                                StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING
                            ],
                        ])
                        ->having([
                            '<', 'count(*)', self::MAX_PLAYER_CT,
                        ])
                        ->groupBy(
                            'match_id'
                        )
                        ->all();

                    if (!empty($storyMatchPlayerCt)) {
                        foreach ($storyMatchPlayerCt as $matchPlayer) {
                            $matchId = $matchPlayer->match_id;
                            $storyMatch = StoryMatch::find()
                                ->where([
                                    'id' => $matchId,
                                    'story_match_status' => [
                                        StoryMatch::STORY_MATCH_STATUS_MATCHING,
                                        StoryMatch::STORY_MATCH_STATUS_PREPARE
                                    ],
                                ])
                                ->andFilterWhere([
                                    'BETWEEN', 'level', $userLevel - 1, $userLevel +1
                                ])
                                ->one();
                            $newPlayerTeam = 2;
                            break;
                        }
                    }
    //                var_dump($storyMatch);exit;
                }

                if (empty($storyMatch)) {

                    if (empty($matchClass)) {
                        $matchClassId = array_rand(StoryMatch::$matchClassRandList);
                        $matchClass = StoryMatch::$matchClassRandList[$matchClassId];
                    } else {
                        if (strpos($matchClass, ',') !== false) {
                            $matchClassArray = explode(',', $matchClass);
                            $matchClassId = array_rand($matchClassArray);
                            $matchClass = $matchClassArray[$matchClassId];
                        }
                    }

                    $storyMatchProp = [
                        'match_class' => $matchClass,
                    ];
                    if (!empty($answerType)) {
                        $storyMatchProp['answer_type'] = $answerType;
                    }

                    // 生成题目
                    $subjects = [];
                    $maxLevel = $userLevel + 2 > 20 ? 20 : $userLevel + 2;
                    switch ($matchClass) {
                        case StoryMatch::MATCH_CLASS_MATH:
                            $ct = 7;
                            $gold = 40;
                            for ($level = $userLevel; $level <= $maxLevel; $level++) {
                                $subjects = array_merge($subjects, $this->generateMathWithCt($ct, $level, $gold));
                            }
                            break;
                        case StoryMatch::MATCH_CLASS_ENGLISH:
                            $ct = 7;
                            for ($level = $userLevel; $maxLevel; $level++) {
                                $subjects = array_merge($subjects, $this->generateEnglishWithCt($ct, $level,0 , $userId));
                            }
                            break;
                        case StoryMatch::MATCH_CLASS_CHINESE:
                            $ct = 7;
                            for ($level = $userLevel; $maxLevel; $level++) {
                                $subjects = array_merge($subjects, $this->generateChineseWithCt($ct, $level,0 , $userId));
                            }
                            break;
                        default:
                            $ct = 7;
                            for ($level = $userLevel; $maxLevel; $level++) {
                                $subjects = array_merge($subjects, $this->generateSubjectsWithCt($ct, $level, $matchClass));
                            }
                            break;

                    }
                    $storyMatchProp['subjects'] = $subjects;
                    $storyMatchProp['subj_source'] = 'db';

                    $storyMatch = new StoryMatch();
                    $storyMatch->story_id = $storyId;
                    $storyMatch->user_id = $userId;
                    $storyMatch->session_id = $sessionId;
                    $storyMatch->match_name = $matchName;
                    $storyMatch->match_type = $matchType;
                    $storyMatch->level = $userLevel;
                    $storyMatch->match_class = $matchClass;
                    $storyMatch->max_players_ct = self::MAX_PLAYER_CT;
                    $storyMatch->story_match_prop = json_encode($storyMatchProp, JSON_UNESCAPED_UNICODE);
                    $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_MATCHING;
                    $storyMatch->join_expire_time = time() + $joinExpireTime;
    //            $storyMatch->match_id = time() . rand(1000, 9999);
                    $storyMatch->save();
                    $matchId = Yii::$app->db->getLastInsertID();
                    $newPlayerTeam = 1;
                }

                if ($newPlayerTeam != 0) {
                    $storyMatchPlayer = new StoryMatchPlayer();
                    $storyMatchPlayer->user_id = $userId;
                    $storyMatchPlayer->team_id = $newPlayerTeam;
                    $storyMatchPlayer->match_id = $matchId;
                    $storyMatchPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING;
                    $storyMatchPlayer->user_model_id = $storyModelId;
                    $storyMatchPlayer->m_story_model_id = $storyModelId;
                    $storyMatchPlayer->m_story_model_detail_id = $storyModelDetailId;
                    $storyMatchPlayer->m_user_model_prop = '';
                    $storyMatchPlayer->save();
                }



    //            if (empty($storyMatch)) {
    ////            $storyMatch = StoryMatch ::find()
    ////                ->where([
    ////                    'user_id' => $userId,
    ////                    'session_id' => $sessionId,
    ////                    'story_id' => $storyId,
    ////                    'm_story_model_id' => $rivalStoryModelIds,
    ////                    'story_match_status' => StoryMatch::STORY_MATCH_STATUS_PREPARE,
    //                $storyMatch = new StoryMatch();
    //                $storyMatch->story_id = $storyId;
    //                $storyMatch->match_type = StoryMatch::MATCH_TYPE_BATTLE;
    //                $storyMatch->user_id = $userId;
    //                $storyMatch->session_id = $sessionId;
    //                $storyMatch->match_name = $matchName;
    //                $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_PREPARE;
    ////            $storyMatch->match_id = time() . rand(1000, 9999);
    //                $storyMatch->save();
    //                $matchId = Yii::$app->db->getLastInsertID();
    //            }

            }
        } else {

            // Todo: 考虑要不要生成一个User
            // 如果不生成，那么UserID需要给一个，并且得判断得出是AI（比如1000000以上）？
            $userLevel = $aiLevel;
            $userInfo = [
                'user_name' => '玩家' . rand(10000, 99999),
            ];
            $avatar = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
            $newPlayerTeam = 99;

            $userId = rand(10000000, 99999999);

            $userModelProp = json_encode([
                'level' => $userLevel,
            ], JSON_UNESCAPED_UNICODE);

            if ($newPlayerTeam != 0) {
                $storyMatchPlayer = new StoryMatchPlayer();
                $storyMatchPlayer->user_id = $userId;
                $storyMatchPlayer->team_id = $newPlayerTeam;
                $storyMatchPlayer->match_id = $matchId;
                $storyMatchPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING;
                $storyMatchPlayer->user_model_id = $storyModelId;
                $storyMatchPlayer->m_story_model_id = $storyModelId;
                $storyMatchPlayer->m_story_model_detail_id = $storyModelDetailId;
                $storyMatchPlayer->m_user_model_prop = $userModelProp;
                $storyMatchPlayer->save();
            }
        }


        $storyMatchPlayers = StoryMatchPlayer::find()
            ->where([
                'match_id' => $matchId,
                'match_player_status' => [
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE,
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_MATCHING,
                    StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING,
                ]
            ])
            ->all();

        if (count($storyMatchPlayers) >= self::MAX_PLAYER_CT) {
            $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_PLAYING;
            $storyMatch->save();

            foreach ($storyMatchPlayers as $storyMatchPlayer) {
                $storyMatchPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PLAYING;
                $storyMatchPlayer->save();
            }
        }


//        if ($ct == 0) {
//            $storyMatchStatus = StoryMatch::STORY_MATCH_STATUS_END;
//            $storyMatchRet = StoryMatch::STORY_MATCH_RESULT_WIN;
//            $storyMatchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_END;
//            $storyMatchPlayerRet = StoryMatchPlayer::STORY_MATCH_RESULT_WIN;
//        } else {
//            $storyMatchStatus = StoryMatch::STORY_MATCH_STATUS_PREPARE;
////            $storyMatchRet = StoryMatch::STORY_MATCH_RESULT_WAITTING;
//            $storyMatchPlayerStatus = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE;
//            $storyMatchPlayerRet = StoryMatchPlayer::STORY_MATCH_RESULT_WAITTING;
//        }

        $msg = '您的比赛准备就绪，准备开始吧！';

        $tmpStoryMatchProp = json_decode($storyMatch->story_match_prop, true);


        return $this->controller->render('race_prepare', [
            'params'        => $_GET,
            'userId'        => $userId,
            'userInfo'      => $userInfo,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'storyMatch'   => $storyMatch,
            'storyMatchProp' => $tmpStoryMatchProp,
            'qaId'          => $qaId,
            'matchId'      => $matchId,
            'answerType'    => 2,
            'msg' => $msg,
            'btnName' => '开始比赛',
            'avatar' => $avatar,
        ]);
    }

    public function  generateMathWithCt($ct, $level = 1, $gold = 0) {
        $subjects = [];
//        $subjs = Yii::$app->qas->generateMath($level, $ct, $gold);
//        $subjs = Yii::$app->qas->generateSubjectWithDoubao($level, StoryMatch::MATCH_CLASS_MATH, $ct, '奥数竞赛题目或者是竞赛题目，复杂度要高一些', [], false);
        $subjs = Yii::$app->qas->getSubjectsFromDbByLevel($level, Subject::SUBJECT_CLASS_MATH, $ct);
        foreach ($subjs as $subj) {
            $subjects[] = [
                'level' => $level,
                'topic' => $subj,
                'max_time' => 180,
            ];
        }
//        for ($i=0; $i<$ct; $i++) {
//            $subjects[] = [
//                'level' => $level,
//                'topic' => Yii::$app->qas->generateMath($level, $ct, $gold),
//                'max_time' => 180,
//            ];
//        }
        return $subjects;
    }

    public function generateSubjectsWithCt($ct, $level = 1, $subjectClass = Subject::SUBJECT_CLASS_NORMAL) {
        $subjects = [];
//        $subjs = Yii::$app->qas->generateMath($level, $ct, $gold);
//        $subjs = Yii::$app->qas->generateSubjectWithDoubao($level, StoryMatch::MATCH_CLASS_MATH, $ct, '奥数竞赛题目或者是竞赛题目，复杂度要高一些', [], false);
        $subjs = Yii::$app->qas->getSubjectsFromDbByLevel($level, $subjectClass, $ct);
        foreach ($subjs as $subj) {
            $subjects[] = [
                'level' => $level,
                'topic' => $subj,
                'max_time' => 180,
            ];
        }
//        for ($i=0; $i<$ct; $i++) {
//            $subjects[] = [
//                'level' => $level,
//                'topic' => Yii::$app->qas->generateMath($level, $ct, $gold),
//                'max_time' => 180,
//            ];
//        }
        return $subjects;
    }

    public function  generateEnglishWithCt($ct, $level = 1, $gold = 0, $userId) {
        $subjects = Yii::$app->qas->generateWordWithChinese($userId, $level, $ct);
        foreach ($subjects as &$sub) {
            $sub['max_time'] = 180;
            $sub['level'] = $level;
        }
        return $subjects;
    }

    public function generateChineseWithCt($ct, $level = 1, $gold = 0, $userId) {
        $subjects = Yii::$app->qas->generateWordWithChinese($userId, $level, $ct);
        foreach ($subjects as &$sub) {
            $sub['max_time'] = 180;
            $sub['level'] = $level;
        }
        return $subjects;
    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
            'answerType' => 1,
        ]);
    }
}