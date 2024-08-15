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
use common\models\Poem;
use common\models\ShopWares;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryRank;
use common\models\User;
use common\models\UserExtends;
use common\models\UserLottery;
use common\models\UserModelLoc;
use common\models\UserModels;
use common\models\UserPrize;
use common\models\UserScore;
use common\models\UserWare;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Practice extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

//        $userModelId = !empty($_GET['user_model_id']) ? $_GET['user_model_id'] : 0;

        $qa = Qa::find()
            ->where([
                'id'    => $qaId,
            ])
            ->one();

        if (!empty($qa)) {
            $qa = $qa->toArray();
            $qa['selected_json'] = \common\helpers\Common::isJson($qa['selected']) ? json_decode($qa['selected'], true) : $qa['selected'];
        }

        $user = User::find()
            ->where([
                'id'    => $userId,
            ])
            ->one();

        if (!empty($user['avatar'])) {
            $user['avatar'] = Attachment::completeUrl($user['avatar']);
        } else {
            $user['avatar'] = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
        }

        if (empty($user)) {
            return $this->renderErr('用户不存在！');
        }

        $userExtends = UserExtends::find()
            ->where([
                'user_id'   => $userId,
            ])
            ->one();

        $userScore = Yii::$app->score->get($userId, $storyId, 0);

        $maxWareLimit = 2;      // 暂时就支持同时2个商品的使用，主要保护AI调用
        $userWares = UserWare::find()
            ->where([
                'user_id' => $userId,
                'status' => UserWare::USER_WARE_STATUS_NORMAL,
                'ware_type' => ShopWares::SHOP_WARE_TYPE_PACKAGE,
            ])
            ->andFilterWhere([
                '>', 'expire_time', time(),
            ])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit($maxWareLimit)
            ->all();

        $userWareIds = [];
        if ( !empty($userWares) ) {
            foreach ($userWares as $userWare) {
                $userWareIds[] = $userWare->id;
            }
        }

        $level = !empty($userExtends['level']) ? $userExtends['level'] : 1;

        $matchClass = !empty($_GET['match_class']) ? $_GET['match_class'] : 0;

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
        $subjects = [];
//        $subjects = Yii::$app->qas->getSubjectsWithUserWare($userId, $matchClass, $level);
        $subjects = Yii::$app->qas->getQaSubjectsWithUserWare($userId, $matchClass, $level);

        switch ($matchClass) {
            case StoryMatch::MATCH_CLASS_MATH:
                // 数学
                // 生成1000道数学题
//                $subjects = [];

                // Todo： 测试用的，上线的话，从订单里找到qaPackage，取出qaIds，然后生成题目
//                $qa = Qa::find()
//                    ->where(['id' => $qaId])
//                    ->one();
//
//                $tmp = Yii::$app->qas->getSubjectWithQa($qa, $matchClass, $level+1, 5);
//                if (!empty($tmp)) {
//                    foreach ($tmp as $t) {
//                        if (mb_strlen($t['topic']) > 10) {
//                            $t['size'] = '40';
//                            $t['speed_rate'] = 0.3;
//                        } else {
//                            $t['size'] = '60';
//                            $t['speed_rate'] = 1;
//                        }
//                        $subjects[] = $t;
//                    }
//                }

//                for ($i=0; $i<1000; $i++) {
//                    // Todo: 测试用
//                    if ($level > 3) $level = 1;
//                    $subjects[] = Yii::$app->qas->generateMath($level);
//                    if ($i == 12) {
//                        $level++;
////                        $subjects[] = $this->generateMath($level);
//                    }
//                }

                $subjectsTmp = Yii::$app->qas->generateMath($level, 1000);
                if (!empty($subjectsTmp)) {
                    foreach ($subjectsTmp as $t) {
                        $subjects[] = $t;
                    }
                }
                break;
            case StoryMatch::MATCH_CLASS_POEM:
                // 生成1000道诗词题
//                $subjects = [];

                for ($i=0; $i<100; $i++) {
                    switch ($level) {
                        case 2:
                            $subjects[] = Yii::$app->qas->generatePoem($level,
                                [Poem::POEM_TYPE_POEM, Poem::POEM_TYPE_POETRY],
                                0, 0, Poem::POEM_ANSWER_TYPE_SENTENCE);
                            break;
                        case 1:
                        default:
                            $subjects[] = Yii::$app->qas->generatePoem($level,
                                [Poem::POEM_TYPE_POEM, Poem::POEM_TYPE_POETRY],
                                0, 0, Poem::POEM_ANSWER_TYPE_WORD);
                            break;
                    }
                    if ($i == 10) {
                        $level++;
//                        $subjects[] = $this->generatePoem($level,
//                            [Poem::POEM_TYPE_POEM, Poem::POEM_TYPE_POETRY],
//                            0, 0,Poem::POEM_ANSWER_TYPE_SENTENCE);
                    }
                }
                break;
            case StoryMatch::MATCH_CLASS_POEM_IDIOM:
//                $subjects = [];
                if ($level > 2) {
                    $level = 2;
                }
                for ($i=0; $i<100; $i++) {
                    switch ($level) {
                        case 2:
                            $subjects[] = Yii::$app->qas->generatePoem($level,
                                Poem::POEM_TYPE_IDIOM,
                                0, 0, Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE);
                            break;
                        case 1:
                        default:
                            $subjects[] = Yii::$app->qas->generatePoem($level,
                                Poem::POEM_TYPE_IDIOM,
                                0, 0,Poem::POEM_ANSWER_TYPE_WORD);
                            break;
                    }
                    if ($i == 10) {
                        $level++;
                    }
                }
                break;
            case StoryMatch::MATCH_CLASS_ENGLISH:
//                $subjects = [];
//                for ($i=0; $i<100; $i++) {
                    $subjects = Yii::$app->qas->generateWordWithChinese(50, $level, 'auto', 'auto');
//                    var_dump($subjects);exit;
//                    if ($i == 10) {
//                        $level++;
//                    }
//                }
                break;
            case StoryMatch::MATCH_CLASS_CHINESE:
                $subjects = Yii::$app->qas->generateChinese($level, 50);
                break;
            default:
                if (empty($subjects)) {
                    return $this->renderErr('比赛类型不支持！');
                }
                break;

        }

//        var_dump($subjects);exit;
//        // 保存比赛状态
//        $storyMatch->match_detail = json_encode($matchDetail, true);
//        $storyMatch->story_match_status = StoryMatch::STORY_MATCH_STATUS_END;
////        $storyMatch->save();
//
////        $matchFlow['flow'] = $matchDetail;

        return $this->controller->render('practice', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'subjects'      => $subjects,
            'matchClass'    => $matchClass,
            'level'         => $level,
            'qa'            => $qa,
            'rtnAnswerType' => 2,
            'subjectsJson' => json_encode($subjects, JSON_UNESCAPED_UNICODE),
            'ct'            => sizeof($subjects),
            'initTimer' => 60,
            'user' => $user,
            'userScore' => $userScore,
            'userWareIdsJson' => json_encode($userWareIds, JSON_UNESCAPED_UNICODE),
        ]);
    }

//    public function generatePoem($level = 1, $poemType = 0, $poemClass = 0, $poemClass2 = 0, $answerType = Poem::POEM_ANSWER_TYPE_WORD) {
//        $prop = [
//            'poem_class' => $poemClass,
//            'poem_class2' => $poemClass2,
//        ];
//        $poem = Yii::$app->qas->getPoemByRand($poemType, $prop, $answerType);
//
//        $hitRange = [
//            5 * (1 + ($level - 1) / 5),
//            10 * (1 + ($level - 1) / 5),
//        ];
//        $gold = 10 * (1 + ($level - 1) / 2);
//
//        $showFormula = $poem['formula'];
//        $answer = $poem['stAnswer'];
//        $formula = $poem['poem'];
//        $answerRange = $poem['selections'];
//
//        $subjects = [
//            'formula' => $showFormula,
//            'answer' => $answer,
//            'standFormula' => $formula,
//            'answerRange' => $answerRange,
//            'level' => $level,
//            'hitRange' => $hitRange,
//            'gold'  => $gold,
//        ];
//
//        return $subjects;
//    }
//
//    public function generateMath($level = 1) {
//        $subjects = [];
//        switch ($level) {
//            case 1:
//                $subjects = Yii::$app->qas->randMathFormula(2, 20, ['+','-'], $level, 1);
//                break;
//            case 2:
//                $randNumCt = rand(2,3);
//                $numMax= 20;
//                if ($randNumCt == 2) {
//                    $numMax = 100;
//                }
//                $subjects = Yii::$app->qas->randMathFormula($randNumCt, $numMax, ['+','-'], $level, 2);
//                break;
//            case 3:
//                $randNumCt = rand(2,3);
//                $numMax= 100;
//                $computeTag = ['+','-',];
//                $mode = 2;
//                if ($randNumCt == 2) {
//                    $numMax = 10;
//                    $computeTag = ['*'];
//                    $mode = 1;
//                }
//                $subjects = Yii::$app->qas->randMathFormula($randNumCt, $numMax, $computeTag, $level, $mode);
//                break;
//            case 4:
//                $randNumCt = rand(2,3);
//                $numMax= 100;
//                $computeTag = ['+','-','*'];
//                $mode = 2;
//                if ($randNumCt == 2) {
//                    $numMax = 10;
//                    $computeTag = ['*','/'];
//                    $mode = 1;
//                }
//                $subjects = Yii::$app->qas->randMathFormula($randNumCt, $numMax, $computeTag, $level, $mode);
//                break;
//        }
//        return $subjects;
//    }

//    public function randMathFormula($numCt = 2, $numMax = 20, $opRange = ['+','-','*','/'], $level, $mode = 1){
//
//        // $mode = 1: 答案最后
//        // $mode = 2: 答案可以在中间
//
//
//        $formula = '';
//
//        $answerTag = 0;
//        $nums = [];
//        $tmpAnswer = 0;
//        $op = '+';
//        $tmpNumMax = $numMax;
//        for ($i=0; $i<$numCt; $i++) {
//
//
//            if ($mode == 2
//                && $answerTag == 0
//            ) {
//                $answerTag1 = rand(0,1);
//                $answerTag = $answerTag1;
//            } else {
//                $answerTag1 = 0;
//            }
//
//            if ($i > 0) {
//                $opIdx = array_rand($opRange);
//                $op = $opRange[$opIdx];
//                if ($op == '-') {
//                    $tmpNumMax = $tmpAnswer;
//                } else {
//                    $tmpNumMax = $numMax;
//                }
//                $nums[] = [
//                    'num'    => $op,
////                    'num'   => $num,
////                    'answerTag' => $answerTag,
//                ];
//            }
//            if ($op == '/') {
//                $num = rand(1, $tmpNumMax);
//            } else {
//                $num = rand(0, $tmpNumMax);
//            }
//
//            $tmpAnswer = eval('return $tmpAnswer ' . $op . ' $num;');
//
//            $nums[] = [
//                'num'  => $num,
//                'answerTag' => $answerTag1,
//            ];
//        }
//
//        $formula = '';
//        foreach ($nums as $numOne) {
//            $formula .= $numOne['num'] . ' ';
//        }
//
//        eval('$ret = ' . $formula . ';');
//
//        $showFormula = '';
//        $isAnswerTag = 0;
//        foreach ($nums as $numOne) {
//            if ($isAnswerTag != 1) {
//                $isAnswerTag = !empty($numOne['answerTag']) ? $numOne['answerTag'] : 0;
//            }
//            if ( !empty($numOne['answerTag']) && $numOne['answerTag'] == 1) {
//                $showFormula .= '? ';
//                $answer = $numOne['num'];
//            } else {
//                $showFormula .= $numOne['num'] . ' ';
//            }
//        }
//        $showFormula .= '= ';
//        if ($isAnswerTag == 1) {
//            $showFormula .= $ret;
//        } else {
//            $showFormula .= '?';
//            $answer = $ret;
//        }
//
//        $answerRange = $this->randAnswerRange($answer, 6);
//
//        // 根据level算伤害范围和金币值
//        $hitRange = [
//            5 * (1 + ($level - 1) / 5),
//            10 * (1 + ($level - 1) / 5),
//        ];
//        $gold = 10 * (1 + ($level - 1) / 2);
//
//        return [
//            'formula' => $showFormula,
//            'answer' => $answer,
//            'standFormula' => $formula,
//            'answerRange' => $answerRange,
//            'level' => $level,
//            'hitRange' => $hitRange,
//            'gold'  => $gold,
//        ];
//
//    }
//
//    public function randAnswerRange($answer, $mis = 5) {
//        $answerRange = [$answer];
//        $range1 = $answer > $mis ? rand($answer - $mis, $answer + $mis) : rand(0, $answer + $mis);
//        if (in_array($range1, $answerRange)) {
//            $range1++;
//        }
//        $answerRange[] = $range1;
////        $range1 = $range1 == $answer ? $range1 + 1 : $range1;
//        $range2 = $range1 + rand(1,$mis);
//        if (in_array($range2, $answerRange)) {
//            $range2 = max($answerRange) + 1;
//        }
//        $answerRange[] = $range2;
//        $range3 = ($range1 - $mis) < 0 ? $range2 + rand(1,$mis) : $range1 - rand(1,$mis);
//        if (in_array($range3, $answerRange)) {
//            $range3 = max($answerRange) + 1;
//        }
//        $answerRange[] = $range3;
////        $answerRange = [
////            $range1,
////            $range2,
////            $range3,
////            $answer
////        ];
//
//        shuffle($answerRange);
//
//        return $answerRange;
//    }

    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}