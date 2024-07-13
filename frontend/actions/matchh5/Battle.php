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
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\helpers\Model;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryRank;
use common\models\User;
use common\models\UserLottery;
use common\models\UserModelLoc;
use common\models\UserModels;
use common\models\UserPrize;
use common\models\UserScore;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Battle extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;


//        $userModelId = !empty($_GET['user_model_id']) ? $_GET['user_model_id'] : 0;


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
        if (!empty($storyMatchPlayers)) {
            foreach ($storyMatchPlayers as $player) {
                if ($player->match_player_status != StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE) {
                    continue;
                }
                if ($player->user_id == $userId) {
                    $myPlayers[] = $player;
                }

                if ($player->user_id == $userId) {
                    $myTeam[$player->team_id][] = $player->id;
                }

                $playerProp = Model::getUserModelProp($player, 'm_user_model_prop');
                $playerAttSpeed = Model::getUserModelPropColWithPropJson($playerProp, 'att_speed');

                $allPlayerProps[$player->id] = $playerProp;

                $teamPlayers[$player->team_id][$player->id] = $player;
                $allPlayers[]   = $player;
                $livePlayers[$playerAttSpeed][]  = $player;
                $playerTeam[$player->id] = $player->team_id;
                $liveTeams[$player->team_id] = $player->user;
            }
        }

        $matchDetail = [];
        $score = 0;
        $round = 0;
        while (count($liveTeams) > 1
            && $round < 100
        ) {
//            var_dump(count($liveTeams));
//            ob_flush();
            ksort($livePlayers);
            $setPlayerAttSpeed = key($livePlayers);
            $currentPlayers = array_shift($livePlayers);

            //            $setPlayerAttSpeed = key($currentPlayers);
            if (count($liveTeams) == 1) {
                break;
            }
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
                        $battleType = 2;
                        $hint = rand($cha * 0.4, $cha * 1.8);
                        $detail = $currentPlayerPetName . '的宠物对' . $rivalPlayerPetName . '发起了进攻，造成了' . $hint . '点伤害！';
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
                    'type' => $battleType,
                    'txt' => $detail,
                    'hint' => $hint,
                ];

                if ($hint > 0) {
                    $rivalPlayerProp = Model::addUserModelPropColWithPropJson($rivalPlayerProp, 'hp', -$hint);
                    $allPlayerProps[$rivalPlayer->id] = $rivalPlayerProp;

//                    $matchDetail[] = '(HP：' . Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp') . ')';

                    if (Model::getUserModelPropColWithPropJson($rivalPlayerProp, 'hp') <= 0) {
                        // 战胜对手
                        $rivalPlayer->match_player_status = StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_INJURED;
//                        $rivalPlayer->save();

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
//                                && 1 != 1
                            ) {
                                $rivalPlayer->userModelLoc->user_model_loc_status = UserModelLoc::USER_MODEL_LOC_STATUS_DEAD;
                                $rivalPlayer->userModelLoc->save();
                                if (!empty($rivalPlayer->userModelLoc->location_id)) {
                                    $rivalUserModelLoc = new UserModelLoc();
                                    $rivalUserModelLoc->user_id = $currentPlayer->user_id;
                                    $rivalUserModelLoc->user_model_id = $currentPlayer->user_model_id;
                                    $rivalUserModelLoc->location_id = $rivalPlayer->userModelLoc->location_id;
                                    $rivalUserModelLoc->story_model_id = $currentPlayer->m_story_model_id;
                                    $rivalUserModelLoc->story_id = $currentPlayer->story_id;
//                                    $rivalUserModelLoc->session_id = $currentPlayer->session_id;
                                    $rivalUserModelLoc->amap_poi_id = $currentPlayer->userModelLoc->amap_poi_id;
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
                }

                $livePlayers[$setPlayerAttSpeed+$currentPlayerAttSpeed][] = $currentPlayer;
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



        $matchDetail = [
            'flow' => $matchDetail,

        ];

        // 保存比赛状态
        $storyMatch->match_detail = json_encode($matchDetail, true);
        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
//        $storyMatch->save();

//        $matchFlow['flow'] = $matchDetail;

        return $this->controller->render('battle', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
//            'userModelId'   => $userModelId,
            'matchDetail'   => $matchDetail,
//            'matchAllFlow' => $matchFlow,
            'matchAllFlowJson' => json_encode($matchDetail, true),
            'storyMatch'   => $storyMatch,
        ]);
    }


    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}