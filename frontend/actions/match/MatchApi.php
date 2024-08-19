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
use common\definitions\Subject;
use common\helpers\Attachment;
use common\helpers\Model;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Qa;
use common\models\SessionQa;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryModelSpecialEff;
use common\models\StoryStages;
use common\models\UserExtends;
use common\models\UserModelLoc;
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

            $needTs = true;
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
                    $needTs = false;
                    $ret = $this->getSuggestionFromSubject();
                    break;
                case 'get_subjects':
                    $needTs = false;
                    $ret = $this->getSubjects();
                    break;
                case 'generate_subjects_to_knockout':
                    $needTs = false;
                    $ret = $this->generateSubjectsToKnockout();
                    break;
                case 'get_subject_by_user_ware_id':
                    $needTs = false;
                    $ret = $this->getSubjectByUserWareId();
                    break;
                case 'play_voice':
                    $ret = $this->playVoice();
                    break;
                case 'battle_for_u3d':
                    $ret = $this->battleForU3d();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
//            var_dump($e);exit;
            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }

        if ($needTs) {
            $ret['ts'] = time();
            $ret['tsf'] = Date('Y-m-d H:i:s', time());
        }

        return $this->success($ret);
    }

    public function getSubjects() {
        $level = !empty($this->_get['level']) ? $this->_get['level'] : 0;
        $matchClass = !empty($this->_get['match_class']) ? $this->_get['match_class'] : 0;
        $ct = !empty($this->_get['ct']) ? $this->_get['ct'] : 10;
        $prompt = !empty($this->_get['prompt']) ? $this->_get['prompt'] : '';
        $needSave = !empty($this->_get['need_save']) ? $this->_get['need_save'] : true;
        $extends = !empty($this->_get['extends']) ? $this->_get['extends'] : [];
//        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
//        $userWareId = !empty($this->_get['user_ware_id']) ? $this->_get['user_ware_id'] : 0;
        return Yii::$app->qas->generateSubjectWithDoubao($level, $matchClass, $ct, $prompt, $extends, $needSave);
    }

    public function generateSubjectsToKnockout() {
        $storyMatchId = !empty($this->_get['story_match_id']) ? $this->_get['story_match_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
//        $ct = !empty($this->_get['ct']) ? $this->_get['ct'] : 10;

        $storyMatch = StoryMatch::find()
            ->where([
                'id' => $storyMatchId,
            ])
            ->one();

        if ($userId != $storyMatch->user_id) {
            return false;
        }

        if (!empty($storyMatch)) {
            $storyMatchProp = json_decode($storyMatch->story_match_prop, true);
            if (!empty($storyMatchProp['subj_source'])
                 && $storyMatchProp['subj_source'] == 'db'
            ) {
                $userLevel = !empty($storyMatchProp['level']) ? $storyMatchProp['level'] : 1;
                $matchClass = !empty($storyMatchProp['match_class']) ? $storyMatchProp['match_class'] : Subject::SUBJECT_CLASS_MATH;
                $subjects = [];
                $maxLevel = $userLevel + 4 > 20 ? 20 : $userLevel + 4;
                switch ($matchClass) {
                    case StoryMatch::MATCH_CLASS_MATH:
                        $ct = 2;
                        for ($level = $userLevel; $level <= $maxLevel; $level++) {
                            $subjects = array_merge($subjects, $this->generateSubjectWithCt($ct, $level, $matchClass));
                        }
                        break;
                    case StoryMatch::MATCH_CLASS_ENGLISH:
                        $ct = 2;
                        for ($level = $userLevel; $maxLevel; $level++) {
                            $subjects = array_merge($subjects, $this->generateSubjectWithCt($ct, $level, $matchClass));
                        }
                        break;

                }
                $saveStoryMatchProp['subjects'] = $subjects;
                $saveStoryMatchProp['subj_source'] = 'doubao';
                $storyMatch->story_match_prop = json_encode($saveStoryMatchProp, JSON_UNESCAPED_UNICODE);
                $storyMatch->save();

                return $storyMatch;
            }
            return false;
        }
        return false;

    }

    public function generateSubjectWithCt($ct, $level, $matchClass) {
        $subjects = [];
        $subjs = Yii::$app->qas->generateSubjectWithDoubao($level, $matchClass, $ct, '奥数竞赛题目或者是竞赛题目，复杂度要高一些', [], false);
        foreach ($subjs as $subj) {
            $subjects[] = [
                'level' => $level,
                'topic' => $subj,
                'max_time' => 180,
            ];
        }
        return $subjects;
    }

    public function getTotalSubjects() {
        $level = !empty($this->_get['level']) ? $this->_get['level'] : 0;
        $matchClass = !empty($this->_get['match_class']) ? $this->_get['match_class'] : 0;
        $ct = !empty($this->_get['ct']) ? $this->_get['ct'] : 10;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        return Yii::$app->qas->generateTotalSubjects($level, $matchClass, $ct, $userId);
    }

    public function getSubjectByUserWareId() {
        $level = !empty($this->_get['level']) ? $this->_get['level'] : 0;
        $matchClass = !empty($this->_get['match_class']) ? $this->_get['match_class'] : 0;
        $ct = !empty($this->_get['ct']) ? $this->_get['ct'] : 10;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $userWareId = !empty($this->_get['user_ware_id']) ? $this->_get['user_ware_id'] : 0;
        return Yii::$app->qas->generateSubjectByUserWareId($userWareId, $level, $matchClass, $ct, $userId);
    }

    public function getSuggestionFromSubject() {

        $topic = !empty($this->_get['topic']) ? $this->_get['topic'] : '';
        $level = !empty($this->_get['level']) ? $this->_get['level'] : 0;

        $ques = !empty($this->_get['ques']) ? $this->_get['ques'] : '';

//        if (empty($ques)) {
//
//            $js = '{
//        "suggestion": "我们可以先假设全是鸡或全是兔，然后根据脚的数量差异来计算。假设全是鸡，应该有 60 只脚，实际 88 只脚，多出来的就是兔子多的脚。",
//        "questions": [
//            "能否用方程求解",
//            "鸡兔数量有上限吗",
//            "脚数计算会出错吗",
//            "还有其他类似题目吗"
//        ],
//        "size": 35,
//        "ts": 1722240449,
//        "tsf": "2024-07-29 16:07:29"
//    }';
//            return json_decode($js, true);
//        }

        $oldMessageJson = !empty($this->_get['old_messages']) ? $this->_get['old_messages'] : '';
        $oldMsgs = json_decode($oldMessageJson, true);
        $oldMessages = [];
        if (!empty($oldMsgs)) {
            foreach ($oldMsgs as $oldMsg) {
                $oldMessages[] = [
                    'role' => !empty($oldMsg['msg_type']) ? $oldMsg['msg_type'] : 'user',
                    'content' => $oldMsg['msg'],
                ];
            }
        }
//        if (!empty($oldMessageJson)) {
//            var_dump($oldMessageJson);
//            var_dump($oldMessages);exit;
//        }

        $matchClass = !empty($this->_get['match_class']) ? $this->_get['match_class'] : 0;

        $suggestions = Yii::$app->doubao->generateSuggestionFromSubject($topic, $level, $matchClass, $ques, $oldMessages);

        $suggestion = !empty($suggestions['SUGGEST']) ? $suggestions['SUGGEST'] : '';
        $questions = !empty($suggestions['QUESTIONS']) ? $suggestions['QUESTIONS'] : [];

        $ret = [
            'suggestion' => $suggestion,
            'questions' => $questions,
            'size' => mb_strlen($suggestion) > 20 ? 35 : 40,
        ];
//        var_dump($ret);exit;

        return $ret;
    }

    public function battleForU3d() {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;


//        $userModelId = !empty($_GET['user_model_id']) ? $_GET['user_model_id'] : 0;

        // 切换到战斗场景
        $expirationInterval = 60;        // 消息超时时间
        $stageUId = 'LJ-WORLD-BATTLE';
        Yii::$app->act->add((int)$sessionId, 0, (int)$storyId, (int)$userId, $stageUId, Actions::ACTION_TYPE_CHANGE_STAGE, $expirationInterval);

        $storyMatch = StoryMatch::find()
            ->where([
                'id'    => $matchId,
                'match_type' => StoryMatch::MATCH_TYPE_BATTLE,
                'story_match_status' => StoryMatch::STORY_MATCH_STATUS_PREPARE,
//                'user_model_id' => $userModelId,
            ])
            ->one();

        if (empty($storyMatch)) {
            return $this->renderErr('战斗还没有准备好！');
//            throw new Exception('您没有准备参赛的赛车，请您用钥匙启动准备好以后，联系小精灵！', ErrorCode::STORY_MATCH_NOT_EXIST_READY);
        }

        $storyMatchPlayers = $storyMatch->players;

        $teamPlayers = [];
        $myPlayers = [];
        $allPlayers = [];
        $livePlayers = [];
        $liveTeams = [];
        $playerTeam = [];
        $allPlayerProps = [];
        $myTeam = [];

        $scenario = [
            ['timeSinceLast' => 0],
        ];

        $tmpScenario = [];
        $createPlayerScenario = [];
        $posMap = [
            'my' => [
                [
                    'z' => 0.5,
                ],
            ],
            'rival' => [
                [
                    'z' => -0.5,
                ],
            ],
        ];
        $playerPos = [];
        $totalSec = 0;
        $createPlayerScenario[] = [
            'performerId' => 'SCIENCE_0',
            'animationName' => 'Create',
            'animationArgs' => [
                'prefab' => 'Grassfield',
                'name' => 'system',
            ],
            'moveX' => 0,
            'moveY' => -1,
            'moveZ' => 0,
        ];
        if (!empty($storyMatchPlayers)) {
            $tmpScenario = ['timeSinceLast' => 1];
            $totalSec += 1;
            foreach ($storyMatchPlayers as $player) {
                if ($player->match_player_status != StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE) {
                    continue;
                }
                if ($player->user_id == $userId) {
                    $myPlayers[] = $player;
                }

                if ($player->user_id == $userId) {
                    $myTeam[$player->team_id][] = $player->id;
                    $playerPos[$player->id] = current($posMap['my']);
                } else {
                    $playerPos[$player->id] = current($posMap['rival']);
                }

                $playerProp = Model::getUserModelProp($player, 'm_user_model_prop');
                $playerAttSpeed = Model::getUserModelPropColWithPropJson($playerProp, 'att_speed');

                $allPlayerProps[$player->id] = $playerProp;

                $teamPlayers[$player->team_id][$player->id] = $player;
                $allPlayers[]   = $player;
                $livePlayers[intval($playerAttSpeed)][]  = $player;
                $playerTeam[$player->id] = $player->team_id;
                $liveTeams[$player->team_id] = $player->user;

                $modelUId = !empty($player->storyModel->model->model_u_id) ? $player->storyModel->model->model_u_id : '';
                $userName = !empty($player->user->user_name) ? $player->user->user_name : '佚名';
                $playerProp = Model::getUserModelProp($player, 'm_user_model_prop');
                // 在脚本里创建角色
                $createPlayerScenario[] = [
                    'performerId' => 'PLAYER_' . $player->id . '_' . $player->user_model_id . '_' . $player->m_story_model_id,
                    'animationName' => 'Create',
                    'animationArgs' => [
                        'prefab' => $modelUId,
                        'name' => $userName,
                    ],
                    'moveX' => !empty($playerPos[$player->id]['x']) ? $playerPos[$player->id]['x'] : 0,
                    'moveY' => !empty($playerPos[$player->id]['y']) ? $playerPos[$player->id]['y'] : 0,
                    'moveZ' => !empty($playerPos[$player->id]['z']) ? $playerPos[$player->id]['z'] : 0,
                    'propBase' => [
                        [
                            'key' => 'hp',
                            'value' => Model::getUserModelPropColWithPropJson($playerProp, 'hp'),
                            'max' => Model::getUserModelPropColWithPropJson($playerProp, 'max_hp'),
                        ],
                    ],
                ];
                $createPlayerScenario[] = [
//                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                    'performerId' => 'WorldRoot',
                    'animationName' => 'Effect',
                    'moveX' => !empty($playerPos[$player->id]['x']) ? $playerPos[$player->id]['x'] : 0,
                    'moveY' => !empty($playerPos[$player->id]['y']) ? $playerPos[$player->id]['y'] : 0,
                    'moveZ' => !empty($playerPos[$player->id]['z']) ? $playerPos[$player->id]['z'] : 0,
                    'animationArgs' => [
                        'animName' => 'EmergeAnimation',
                        'endTime' => 1,
                    ],
                ];
            }
        }
        $tmpScenario['lstPerforms'] = $createPlayerScenario;

        $scenario[] = $tmpScenario;

        $matchDetail = [];
        $score = 0;
        $round = 0;

        $specialEffs = [];

        $oldPlayerAttSpeed = 0;
        $setPlayerAttSpeed = 0;

        while (count($liveTeams) > 1
            && $round < 100
        ) {
//            var_dump(count($liveTeams));
//            ob_flush();
            ksort($livePlayers);
            $oldPlayerAttSpeed = $setPlayerAttSpeed;
            $setPlayerAttSpeed = key($livePlayers);
//            $currentPlayers = array_shift($livePlayers);
            $currentPlayers = current($livePlayers);
            unset($livePlayers[$setPlayerAttSpeed]);

            //            $setPlayerAttSpeed = key($currentPlayers);
            if (count($liveTeams) == 1) {
                break;
            }
            $detail = '';
            foreach ($currentPlayers as $currentPlayer) {

                $currentTeam = $playerTeam[$currentPlayer->id];
                $againstPlayers = [];

                foreach ($teamPlayers as $teamId => $teamPlayer) {
                    if ($teamId == $currentTeam) {
                        continue;
                    }
                    $againstPlayers = array_merge($againstPlayers, $teamPlayer);
                }

                $rivalPlayer = !empty($againstPlayers[array_rand($againstPlayers)]) ? $againstPlayers[array_rand($againstPlayers)] : null;
                if (empty($rivalPlayer)) {
                    break;
                }

//                $currentPlayerProp = Model::getUserModelProp($currentPlayer, 'm_user_model_prop');
                $currentPlayerProp = $allPlayerProps[$currentPlayer->id];
                $currentPlayerLevel = Model::getUserModelPropColWithPropJson($currentPlayerProp, 'level');
                $currentPlayerEffClass = Model::getUserModelPropColWithPropJson($currentPlayerProp, 'eff_class');
                $currentPlayerAttack = Model::getUserModelPropColWithPropJson($currentPlayerProp, 'attack');
                $currentPlayerAgility = Model::getUserModelPropColWithPropJson($currentPlayerProp, 'agility');
//            $currentPlayerDefence = Model::getUserModelPropColWithPropJson($currentPlayerProp, 'defense');
                $currentPlayerAttSpeed = Model::getUserModelPropColWithPropJson($currentPlayerProp, 'att_speed');
//                $rivalPlayerProp = Model::getUserModelProp($rivalPlayer, 'm_user_model_prop');
                $rivalPlayerProp = $allPlayerProps[$rivalPlayer->id];
                $rivalPlayerLevel = Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'level');
//            $rivalPlayerAttack = Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'attack');
                $rivalPlayerDefence = Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'defense');
                $rivalPlayerAgility = Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'agility');
//            $rivalPlayerAttSpeed = Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'att_speed');

                // 随机物理攻击/魔法攻击
                if (empty($specialEffs[$currentPlayer->id])) {
                    $storyModelId = $currentPlayer->m_story_model_id;
                    $specEffs = StoryModelSpecialEff::find()
                        ->where([
                            'or',
                            ['own_story_model_id' => $storyModelId],
                            ['own_story_model_id' => 0],
                        ]);
                    if (!empty($currentPlayerLevel)) {
                        $specEffs = $specEffs->andFilterWhere([
                           '<=', 'level', $currentPlayerLevel,
                        ]);
                    }
                    $specEffs = $specEffs->orderBy([
                            'own_story_model_id' => SORT_DESC,
                        ])
//                        ->asArray()
                        ->all();

                    if (!empty($specEffs)) {
                        foreach ($specEffs as $specEff) {
                            $modelUId = !empty($specEff->model->model_u_id) ? $specEff->model->model_u_id : '';
                            $specEff = $specEff->toArray();
                            $specEffProp = json_decode($specEff['prop'], true);
                            $specialEffs[$currentPlayer->id][] = [
                                'model_u_id' => $modelUId,
                                'eff_class' => $specEff['eff_class'],
                                'eff_mode' => $specEff['eff_mode'],
                                'during_ti' => $specEff['during_ti'],
                                'cd' => $specEff['cd'],
                                'prop' => $specEffProp,
                                'attack' => Model::getUserModelPropColWithPropJson($specEffProp, 'attack'),
                            ];
                        }
                    }
                    $specialEffs[$currentPlayer->id][] = [
                        'attack' => 0,
                    ];
                }

                if (!empty($specialEffs[$currentPlayer->id])) {
                    $eff = $specialEffs[$currentPlayer->id][array_rand($specialEffs[$currentPlayer->id])];
                    if (!empty($eff['attack'])) {
                        $currentPlayerAttack = $eff['attack'] * pow(1.04, $currentPlayerLevel);
                    }
                } else {
                    $eff = [];
                }

                $attackRand = rand($currentPlayerAttack * 0.8, $currentPlayerAttack * 1.2);
                $defenceRand = rand($rivalPlayerDefence * 0.8, $rivalPlayerDefence * 1.2);

                $cha = ($attackRand * 4 - $defenceRand) / 10;

                $agilityCha = $currentPlayerAgility - $rivalPlayerAgility;
                $missRand = rand(0, 1000);

                if ($currentPlayer->user_id == $userId) {
                    $currentPlayerPetName = '你';
                } else {
                    $currentPlayerPetName = !empty($currentPlayer->user->user_name) ? $currentPlayer->user->user_name : '地盘小霸王';
                }
                if ($rivalPlayer->user_id == $userId) {
                    $rivalPlayerPetName = '你';
                } else {
                    $rivalPlayerPetName = !empty($rivalPlayer->user->user_name) ? $rivalPlayer->user->user_name : '地盘小霸王';
                }
//                $rivalPlayerPetName = !empty($rivalPlayer->user->user_name) ? $rivalPlayer->user->user_name : '地盘小霸王';


                if ($missRand < $agilityCha) {
                    $hint = -1; // 闪避
                    $battleType = 1;
                    $detail = $currentPlayerPetName . '的宠物对' . $rivalPlayerPetName . '发起了进攻，但是被闪开了！';
                } else {
                    if ($cha > 0) {
//                        $battleType = 2;
                        $hint = rand($cha * 0.4, $cha * 1.8);
//                        $detail = $currentPlayerPetName . '的宠物对' . $rivalPlayerPetName . '发起了进攻，造成了' . $hint . '点伤害！';

                    } else {
                        $hint = 0;
                        $battleType = 3;
                        $detail = $currentPlayerPetName . '的宠物对' . $rivalPlayerPetName . '发起了进攻，但是被格挡了！';
                    }
                }
                $detail .= '(HP：' . Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp') . ')';
//                $detail .= count($liveTeams) . '！';

                if (isset($myTeam[$currentPlayer->team_id])) {
                    $direction = 1;
                } else {
                    $direction = -1;
                }
                $matchDetail[] = [
                    'rivalPlayerId' => $rivalPlayer->id,
                    'rivalPlayerPetName' => $rivalPlayerPetName,
                    'rivalPlayer' => $rivalPlayer,
                    'rivalAvatar' => !empty($rivalPlayer->storyModel->icon) ? Attachment::completeUrl($rivalPlayer->storyModel->icon, true) : '',
                    'currentPlayerId' => $currentPlayer->id,
                    'currentPlayerPetName' => $currentPlayerPetName,
                    'currentPlayer' => $currentPlayer,
                    'currentAvatar' => !empty($currentPlayer->storyModel->icon) ? Attachment::completeUrl($currentPlayer->storyModel->icon, true) : '',
                    'restHp' => Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp'),
                    'maxHp' => Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'max_hp'),
                    'direction' => $direction,
                    'type' => !empty($battleType) ? $battleType : 0,
                    'txt' => !empty($detail) ? $detail : '',
                    'hint' => $hint,
                ];

                $hitPlayerScenario = [];
                $playerScenario = [];
                $prePlayerScenario = [];
                if ($hint > 0) {
                    $rivalPlayerProp = Model::addUserModelPropColWithPropJson($rivalPlayerProp, 'hp', -$hint);
                    $allPlayerProps[$rivalPlayer->id] = $rivalPlayerProp;

                    $attScenario = [];
                    $rivScenario = [];
                    if (empty($eff['eff_mode'])) {
                        // 普通攻击
                        $duration = 1;
                        $prePlayerScenario[] = [

                        ];
                        $playerScenario[] = [
                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'animationName' => 'Slide',
                            'moveZ' => 0.7,
                            'slideSpeed' => 5,
                        ];
                        $playerScenario[] = [
                            'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                            'animationName' => 'Slide',
                            'moveZ' => -0.3,
                        ];
                        $hitPlayerScenario[] = [
                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'animationName' => 'Slide',
                            'moveZ' => -0.7,
                            'slideSpeed' => 2,
                        ];
                    } else {
                        // 魔法攻击
                        $duration = !empty($eff['during_ti']) ? $eff['during_ti'] : 4.5;

                        $effPosZ = 0;
                        if (!empty($eff['eff_mode'])) {
                            switch ($eff['eff_mode']) {
                                case StoryModelSpecialEff::EFF_MODE_OWNER:
                                    $effPosZ = !empty($playerPos[$currentPlayer->id]['z'])
                                        ? $playerPos[$currentPlayer->id]['z'] : 0;
                                    break;
                                case StoryModelSpecialEff::EFF_MODE_RIVAL:
                                    $effPosZ = !empty($playerPos[$rivalPlayer->id]['z'])
                                        ? $playerPos[$rivalPlayer->id]['z'] : 0;
                                    break;
                                default:
                                    $effPosZ = 0;
                                    break;
                            }
                        }

                        $prePlayerScenario[] = [
                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'animationName' => 'Slide',
//                            'moveZ' => 0.2,
                            'moveY' => 0.5,
                            'slideSpeed' => 1,
                        ];
                        $playerScenario[] = [
//                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'performerId' => 'WorldRoot',
                            'animationName' => 'Effect',
                            'moveZ' => $effPosZ,
                            'animationArgs' => [
                                'animName' => $eff['model_u_id'],
                                'endTime' => $duration,
                            ],
                        ];
                        $playerScenario[] = [
                            'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                            'animationName' => 'Slide',
                            'moveZ' => -0.3,
                        ];
                        $hitPlayerScenario[] = [
                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'animationName' => 'Slide',
//                            'moveZ' => -0.2,
                            'moveY' => -0.5,
                            'slideSpeed' => 5,
                        ];
                    }
                    $hitPlayerScenario[] = [
                        'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                        'animationName' => 'PopText',
                        'animationArgs' => $hint,
                    ];
                    $hitPlayerScenario[] = [
                        'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                        'animationName' => 'Slide',
                        'moveZ' => 0.3,
                    ];

                    $playerScenario[] = [
                        'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                        'animationName' => 'UpdateInfo',
                        'propBase' => [
                            [
                                'key' => 'hp',
                                'value' => Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp'),
                                'max' => Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'max_hp'),
                            ]
                        ],
                    ];
                } else {
                    $playerScenario[] = [
                        'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                        'animationName' => 'Slide',
                        'moveZ' => 0.7,
                        'slideSpeed' => 5,
                    ];
                    $playerScenario[] = [
                        'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                        'animationName' => 'Slide',
                        'moveZ' => -0.3,
                    ];
                    $hitPlayerScenario[] = [
                        'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                        'animationName' => 'Slide',
                        'moveZ' => -0.7,
                        'slideSpeed' => 2,
                    ];
                    $hitPlayerScenario[] = [
                        'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                        'animationName' => 'Slide',
                        'moveZ' => 0.3,
                    ];
                    $hitPlayerScenario[] = [
                        'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                        'animationName' => 'PopText',
                        'animationArgs' => !empty($battleType) && $battleType == 1 ? 'MISS' : '格挡',
                    ];
                }

                    $tsl = $setPlayerAttSpeed - $oldPlayerAttSpeed;
                    $tsl = $tsl/10;
                    $scenario[] = [
                        'timeSinceLast' => $tsl,
                        'lstPerforms' => $prePlayerScenario,
                    ];
                    $scenario[] = [
                        'timeSinceLast' => 0.5,
                        'lstPerforms' => $playerScenario,
                    ];
                    $scenario[] = [
                        'timeSinceLast' => $duration,
                        'lstPerforms' => $hitPlayerScenario,
                    ];

                    $totalSec += $tsl + 0.5 + $duration;

//                    $matchDetail[] = '(HP：' . Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp') . ')';

                    if (Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp') <= 0) {
                        // 战胜对手
                        $rivalPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_INJURED;
//                        $rivalPlayer->save();

                        $loseScenario = [];
                        $loseScenario[] = [
                            'performerId' => 'PLAYER_' . $rivalPlayer->id . '_' . $rivalPlayer->user_model_id . '_' . $rivalPlayer->m_story_model_id,
                            'animationName' => 'Delete',
                        ];
                        $loseScenario[] = [
//                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'performerId' => 'WorldRoot',
                            'animationName' => 'Effect',
                            'moveX' => !empty($playerPos[$rivalPlayer->id]['x']) ? $playerPos[$rivalPlayer->id]['x'] : 0,
                            'moveY' => !empty($playerPos[$rivalPlayer->id]['y']) ? $playerPos[$rivalPlayer->id]['y'] : 0,
                            'moveZ' => !empty($playerPos[$rivalPlayer->id]['z']) ? $playerPos[$rivalPlayer->id]['z'] : 0,
                            'animationArgs' => [
                                'animName' => 'EmergeAnimation',
                                'endTime' => 1,
                            ],
                        ];

                        $scenario[] = [
                            'timeSinceLast' => 1,
                            'lstPerforms' => $loseScenario,
                        ];

                        $winScenario = [];
                        $winScenario[] = [
                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'animationName' => 'Animation',
                            'animationArgs' => [
                                'animName' => 'Play',
                                'endTime' => 2,
                            ],
                        ];

                        $scenario[] = [
                            'timeSinceLast' => 1,
                            'lstPerforms' => $winScenario,
                        ];

                        $winScenario = [];
                        $winScenario[] = [
                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'animationName' => 'Delete',
                        ];
                        $winScenario[] = [
                            'performerId' => 'SCIENCE_0',
                            'animationName' => 'Delete',
                        ];
                        $winScenario[] = [
//                            'performerId' => 'PLAYER_' . $currentPlayer->id . '_' . $currentPlayer->user_model_id . '_' . $currentPlayer->m_story_model_id,
                            'performerId' => 'WorldRoot',
                            'animationName' => 'Effect',
                            'moveX' => !empty($playerPos[$currentPlayer->id]['x']) ? $playerPos[$currentPlayer->id]['x'] : 0,
                            'moveY' => !empty($playerPos[$currentPlayer->id]['y']) ? $playerPos[$currentPlayer->id]['y'] : 0,
                            'moveZ' => !empty($playerPos[$currentPlayer->id]['z']) ? $playerPos[$currentPlayer->id]['z'] : 0,
                            'animationArgs' => [
                                'animName' => 'EmergeAnimation',
                                'endTime' => 1,
                            ],
                        ];

                        $scenario[] = [
                            'timeSinceLast' => 5,
                            'lstPerforms' => $winScenario,
                        ];

                        $totalSec += 7;

                        // 如果是我的宠物，加经验和金币
                        if ($currentPlayer->user_id == $userId) {
                            $levelCha = $currentPlayerLevel - $rivalPlayerLevel;
                            if ($levelCha >= 10) {
                                $exp = 0;
                                $score += 0;
                            } else {
                                if (!empty(Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'exp_base'))) {
                                    $expBase = Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'exp_base');
                                } else {
                                    $expBase = 10 * pow(1.15, $currentPlayerLevel);
                                }
                                $expBei = (10 - $levelCha) / 10;
                                //                    $expBei = ($expBei > 0) ? $expBei : 0;
                                $exp = $expBase * $expBei;

                                if (!empty(Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'score_base'))) {
                                    $scoreBase = Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'score_base');
                                } else {
                                    $scoreBase = 5 * pow(1.15, $currentPlayerLevel);
                                }
                                $scoreBei = (10 - $levelCha) / 10;
                                $score += $scoreBase * $scoreBei;
                            }

                            // Todo: 暂时去掉保存，为了调试，最后统一打开
                            if (!empty($rivalPlayer->userModelLoc)
                                && 1 != 1
                            ) {
                                $rivalPlayer->userModelLoc->user_model_loc_status = UserModelLoc::USER_MODEL_LOC_STATUS_DEAD;
                                $rivalPlayer->userModelLoc->save();
                                if (!empty($rivalPlayer->userModelLoc->location_id)) {
                                    $rivalUserModelLoc = new UserModelLoc();
                                    $rivalUserModelLoc->user_id = $currentPlayer->user_id;
                                    $rivalUserModelLoc->user_model_id = $currentPlayer->user_model_id;
                                    $rivalUserModelLoc->location_id = $rivalPlayer->userModelLoc->location_id;
                                    $rivalUserModelLoc->story_model_id = $currentPlayer->m_story_model_id;
                                    $rivalUserModelLoc->active_class = $rivalPlayer->userModelLoc->active_class;
                                    $rivalUserModelLoc->amap_poi_id = $rivalPlayer->userModelLoc->amap_poi_id;
//                                    $rivalUserModelLoc->story_id = $currentPlayer->story_id;
//                                    $rivalUserModelLoc->session_id = $currentPlayer->session_id;
//                                    $rivalUserModelLoc->amap_poi_id = $currentPlayer->userModelLoc->amap_poi_id;
                                    $rivalUserModelLoc->user_model_prop = $currentPlayer->m_user_model_prop;
                                    $rivalUserModelLoc->user_model_loc_status = UserModelLoc::USER_MODEL_LOC_STATUS_LIVE;
                                    $rivalUserModelLoc->save();
                                }
                            }

                            $currentPlayerProp = Model::addUserModelPropColWithPropJson($currentPlayerProp, 'exp', $exp);
                            $allPlayerProps[$currentPlayer->id] = $currentPlayerProp;

                            $tmpPlayerProp = Yii::$app->models->checkLevel($currentPlayerProp);
                            $currentPlayerProp = $tmpPlayerProp['data'];

//                            $currentPlayerProp = Model::addUserModelPropColWithPropJson($currentPlayerProp, 'score', $score);
                            $currentPlayer->m_user_model_prop = json_encode($currentPlayerProp, true);

                            // Todo: 暂时去掉保存，为了调试，最后统一打开
//                            $currentPlayer->save();

                            if (!empty($currentPlayer->userModel)) {
                                $currentPlayer->userModel->user_model_prop = Model::addUserModelPropCol($currentPlayer->userModel, 'exp', $exp);

                                // Todo: 暂时去掉保存，为了调试，最后统一打开
//                                $currentPlayer->userModel->save();
                            }

                        }

                        unset($teamPlayers[$rivalPlayer->team_id][$rivalPlayer->id]);
                        if (count($teamPlayers[$rivalPlayer->team_id]) == 0) {
                            unset($teamPlayers[$rivalPlayer->team_id]);
                            unset($liveTeams[$rivalPlayer->team_id]);
                        }

//                        var_dump($liveTeams);
                    }
//                }

                $livePlayers[(int)$setPlayerAttSpeed + (int)$currentPlayerAttSpeed][] = $currentPlayer;
            }
            $round++;

        }



        if (!empty($allPlayers)) {
            foreach ($allPlayers as $player) {
                $player->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_END;
//                $player->save();
            }
        }
        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
//                        $storyMatch->save();

//        $matchDetail[] = '比赛结束！';

        if (!empty($liveTeams)) {
            $liveUser = current($liveTeams);

            if (!empty($liveUser) && $liveUser->id == $userId) {
                $storyMatch->ret = StoryMatch::STORY_MATCH_RESULT_WIN;
                $matchDetail[] = [
                    'rivalPlayerId' => $rivalPlayer->id,
                    'rivalPlayerPetName' => $rivalPlayerPetName,
                    'currentPlayerId' => $currentPlayer->id,
                    'currentPlayerPetName' => $currentPlayerPetName,
                    'restHp' => Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp'),
                    'type' => 9,
                    'txt' => '你的宠物击败了对手！',
                    'fightRet' => 1,
                ];

                $userScore = UserScore::find()
                    ->where([
                        'user_id' => $userId,
                        'story_id' => $storyId,
                    ])
                    ->one();
                if (empty($userScore)) {
                    $userScore = new UserScore();
                    $userScore->user_id = $userId;
                    $userScore->story_id = $storyId;
                    $userScore->score = $score;
                    $userScore->save();
                } else {
                    $userScore->score += $score;
                    $userScore->save();
                }
            } else {
                $storyMatch->ret = StoryMatch::STORY_MATCH_RESULT_LOSE;
                $matchDetail[] = [
                    'rivalPlayerId' => $rivalPlayer->id,
                    'rivalPlayerPetName' => $rivalPlayerPetName,
                    'currentPlayerId' => $currentPlayer->id,
                    'currentPlayerPetName' => $currentPlayerPetName,
                    'restHp' => Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp'),
                    'type' => 9,
                    'txt' => '你的宠物不幸战败了！',
                    'fightRet' => 2,
                ];
            }
        }

        // 发送Action结束游戏
        $expirationInterval = 600;
        $endScenario = json_encode([
            'timeDelayAction' => $totalSec,
            'showModels' => ['LJ-WORLD-SPIRIT-BATTLE1'],
            'hideModels' => ['LJ-WORLD-SPIRIT-BATTLE']
        ], JSON_UNESCAPED_UNICODE);
        Yii::$app->act->addWithoutTag($sessionId, 0, $storyId, $userId, $endScenario, Actions::ACTION_TYPE_MODEL_DISPLAY, $expirationInterval, 0);
//        LJ-WORLD-SPIRIT-BATTLE1


        $matchDetail = [
            'flow' => $matchDetail,

        ];

        // 保存比赛状态
        $storyMatch->match_detail = json_encode($matchDetail, true);
        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
//        $storyMatch->save();

//        $matchFlow['flow'] = $matchDetail;

        $setData = [
            'source' => 'battle',
        ];
        Yii::$app->knowledge->setDailyMissions($userId, $storyId, $sessionId, $setData);
//var_dump($scenario);exit;
        $expirationInterval = 600;
        Yii::$app->act->addWithTag($sessionId, 0, $storyId, $userId, $scenario, Actions::ACTION_TYPE_MODEL_DISPLAY, $expirationInterval, 0, 'performList');

        return [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
//            'userModelId'   => $userModelId,
            'matchDetail'   => $matchDetail,
//            'matchAllFlow' => $matchFlow,
            'matchAllFlowJson' => json_encode($matchDetail, true),
            'storyMatch'   => $storyMatch,
            'scenario'      => $scenario,
        ];
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

    public function playVoice() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $messages = !empty($this->_get['messages']) ? $this->_get['messages'] : '';

        $ret = Yii::$app->doubaoTTS->ttsWithDoubao($messages, $userId);

        if (!empty($ret['file'])) {
            $ret['file']['url'] = Attachment::completeUrl($ret['file']['file'], false);
        }

        return $ret;
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