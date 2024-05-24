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

class Challenge extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

//        $userModelId = !empty($_GET['user_model_id']) ? $_GET['user_model_id'] : 0;

        $qa = Qa::find()
            ->where([
                'id'    => $qaId,
            ])
            ->one();

        $qa = $qa->toArray();

        $qa['selected_json'] = \common\helpers\Common::isJson($qa['selected']) ? json_decode($qa['selected'], true) : $qa['selected'];

        $storyMatch = StoryMatch::find()
            ->where([
                'id'    => $matchId,
                'match_type' => StoryMatch::MATCH_TYPE_CHALLENGE,
                'story_match_status' => StoryMatch::STORY_MATCH_STATUS_PREPARE,
//                'user_model_id' => $userModelId,
            ])
            ->one();

        $user = User::find()
            ->where([
                'id'    => $userId,
            ])
            ->one();

        if (empty($user)) {
            return $this->renderErr('用户不存在！');
        }

        if (empty($storyMatch)) {
            return $this->renderErr('挑战还没有准备好！');
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
        $grade = 1;

        $myFormula = [

        ];
        if (!empty($storyMatchPlayers)) {
            foreach ($storyMatchPlayers as $player) {
                if ($player->match_player_status != StoryMatchPlayer::STORY_MATCH_PLAYER_STATUS_PREPARE) {
                    continue;
                }
                if ($player->user_id == $userId) {
                    $myPlayers[] = $player;
                    $grade = !empty($player->grade) ? $player->grade : 1;
                }

                if ($player->user_id == $userId) {
                    $myTeam[$player->team_id][] = $player->id;
                }

                $playerProp = Model::getUserModelProp($player, 'm_user_model_prop');
                $playerAttSpeed = Model::getUserModelPropColWithPropJson($playerProp, 'att_speed');
                $playerAttack = Model::getUserModelPropColWithPropJson($playerProp, 'attack');

                if ($player->user_id != $userId) {
                    $rivalPlayers[] = [
                        'player' => $player,
                        'prop' => $playerProp,
                        'att_speed' => $playerAttSpeed,
                        'attack' => $playerAttack,
                    ];
                } else {
                    $hit = rand(5,10) + 12 * pow(1.02, ($grade - 1)) + 5;
                    $hp = rand(20,40) * pow(1.02, ($grade - 1)) + 200;
                    $myProp = [
                        'hit' => [
                            'min' => $hit * 0.8,
                            'max' => $hit * 1.2,
                        ],
                        'hp' => $hp
                    ];
                }

//                $allPlayerProps[$player->id] = $playerProp;
//
//                $teamPlayers[$player->team_id][$player->id] = $player;
//                $allPlayers[]   = $player;
//                $livePlayers[$playerAttSpeed][]  = $player;
//                $playerTeam[$player->id] = $player->team_id;
//                $liveTeams[$player->team_id] = $player->user;
            }
            foreach ($rivalPlayers as &$rivalPlayer) {
                $playerAttSpeed = $rivalPlayer['att_speed'];
                $playerAttack = $rivalPlayer['attack'];
//                $grade = 20;
//                var_dump(pow($grade, 0.3));exit;
                $rivalPlayer['show_speed'] = ($playerAttSpeed * 110) * (pow($grade, 0.5));
//                var_dump($rivalPlayer['show_speed']);exit;
                $rivalPlayer['show_attack'] = [
                    'min' => $playerAttack * 0.4,
                    'max' => $playerAttack * 0.6
                ];
            }
//            var_dump($rivalPlayers);exit;
        }

        if ($storyMatch->match_class == StoryMatch::MATCH_CLASS_MATH) {
            // 数学
            // 生成1000道数学题
            $subjects = [];

            $level = 1;
            switch ($grade) {
                case 1:
                default:
                    $level = 1;
                    break;
            }

            for ($i=0; $i<1000; $i++) {
                $subjects[] = $this->generateMath($level);
                if ($i == 30) {
                    $level = 2;
                    $subjects[] = $this->generateMath($level);
                }
            }
        }

//        var_dump($subjects);exit;
//        // 保存比赛状态
//        $storyMatch->match_detail = json_encode($matchDetail, true);
//        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
////        $storyMatch->save();
//
////        $matchFlow['flow'] = $matchDetail;

        return $this->controller->render('challenge', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'matchId'       => $matchId,
            'subjects'      => $subjects,
            'matchPlayers'  => $storyMatchPlayers,
            'myPlayers'     => $myPlayers,
            'myProp'        => $myProp,
            'myPropJson'    => json_encode($myProp),
            'rivalPlayers'  => $rivalPlayers,
            'qa'            => $qa,
            'rtnAnswerType' => 2,
            'subjectsJson' => json_encode($subjects),
            'ct'            => sizeof($subjects),
            'storyMatch'   => $storyMatch,
            'initTimer' => 60,
        ]);
    }

    public function generateMath($level = 1) {
        $subjects = [];
        if ($level == 1) {
            $subjects = $this->randMathFormula(2, 20, ['+','-'], $level, 1);
        } else if ($level == 2) {
            $randNumCt = rand(2,3);
            $numMax= 20;
            if ($randNumCt == 2) {
                $numMax = 100;
            }
            $subjects = $this->randMathFormula($randNumCt, $numMax, ['+','-'], $level, 2);
        }
        return $subjects;
    }

    public function randMathFormula($numCt = 2, $numMax = 20, $opRange = ['+','-','*','/'], $level, $mode = 1){

        // $mode = 1: 答案最后
        // $mode = 2: 答案可以在中间


        $formula = '';

        $answerTag = 0;
        $nums = [];
        $tmpAnswer = 0;
        $op = '+';
        $tmpNumMax = $numMax;
        for ($i=0; $i<$numCt; $i++) {


            if ($mode == 2
                && $answerTag == 0
            ) {
                $answerTag1 = rand(0,1);
                $answerTag = $answerTag1;
            } else {
                $answerTag1 = 0;
            }

            if ($i > 0) {
                $opIdx = array_rand($opRange);
                $op = $opRange[$opIdx];
                if ($op == '-') {
                    $tmpNumMax = $tmpAnswer;
                } else {
                    $tmpNumMax = $numMax;
                }
                $nums[] = [
                    'num'    => $op,
//                    'num'   => $num,
//                    'answerTag' => $answerTag,
                ];
            }
            if ($op == '/') {
                $num = rand(1, $tmpNumMax);
            } else {
                $num = rand(0, $tmpNumMax);
            }

            $tmpAnswer = eval('return $tmpAnswer ' . $op . ' $num;');

            $nums[] = [
                'num'  => $num,
                'answerTag' => $answerTag1,
            ];
        }

        $formula = '';
        foreach ($nums as $numOne) {
            $formula .= $numOne['num'] . ' ';
        }

        eval('$ret = ' . $formula . ';');

        $showFormula = '';
        $isAnswerTag = 0;
        foreach ($nums as $numOne) {
            if ($isAnswerTag != 1) {
                $isAnswerTag = !empty($numOne['answerTag']) ? $numOne['answerTag'] : 0;
            }
            if ( !empty($numOne['answerTag']) && $numOne['answerTag'] == 1) {
                $showFormula .= '? ';
                $answer = $numOne['num'];
            } else {
                $showFormula .= $numOne['num'] . ' ';
            }
        }
        $showFormula .= '= ';
        if ($isAnswerTag == 1) {
            $showFormula .= $ret;
        } else {
            $showFormula .= '?';
            $answer = $ret;
        }

        $answerRange = $this->randAnswerRange($answer, 6);

        // 根据level算伤害范围和金币值
        $hitRange = [
            5 * (1 + ($level - 1) / 5),
            10 * (1 + ($level - 1) / 5),
        ];
        $gold = 10 * (1 + ($level - 1) / 2);

        return [
            'formula' => $showFormula,
            'answer' => $answer,
            'standFormula' => $formula,
            'answerRange' => $answerRange,
            'level' => $level,
            'hitRange' => $hitRange,
            'gold'  => $gold,
        ];

    }

    public function randAnswerRange($answer, $mis = 5) {
        $answerRange = [$answer];
        $range1 = $answer > $mis ? rand($answer - $mis, $answer + $mis) : rand(0, $answer + $mis);
        if (in_array($range1, $answerRange)) {
            $range1++;
        }
        $answerRange[] = $range1;
//        $range1 = $range1 == $answer ? $range1 + 1 : $range1;
        $range2 = $range1 + rand(1,$mis);
        if (in_array($range2, $answerRange)) {
            $range2 = max($answerRange) + 1;
        }
        $answerRange[] = $range2;
        $range3 = ($range1 - $mis) < 0 ? $range2 + rand(1,$mis) : $range1 - rand(1,$mis);
        if (in_array($range3, $answerRange)) {
            $range3 = max($answerRange) + 1;
        }
        $answerRange[] = $range3;
//        $answerRange = [
//            $range1,
//            $range2,
//            $range3,
//            $answer
//        ];

        shuffle($answerRange);

        return $answerRange;
    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}