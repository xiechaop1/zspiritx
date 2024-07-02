<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\ErrorCode;
use common\helpers\Attachment;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Poem;
use common\models\Qa;
use common\models\QaPackage;
use common\models\Session;
use common\models\UserKnowledge;
use common\models\UserQa;
use common\models\UserScore;
use liyifei\chinese2pinyin\Chinese2pinyin;
use yii\base\Component;
use yii;

class Qas extends Component
{

    public function getSubjectWithQa($qaModel, $matchClass = 0, $level = 1, $ct = 1) {
        $ret = [];
        switch ($qaModel->qa_type) {
            case Qa::QA_TYPE_CHATGPT:
            default:
                if (!empty($qaModel->prop)) {
                    $qaProp = json_decode($qaModel->prop, true);
                    if (!empty($qaProp['prompt'])) {
                        $prompt = $qaProp['prompt'];

                        if (!empty($qaProp['ct'])) {
                            $ct = $qaProp['ct'];
                        }

                        if (!empty($qaProp['level'])) {
                            $level = $qaProp['level'];
                        }

                        $subjects = Yii::$app->doubao->generateSubject($prompt, $level, $matchClass, $ct);

//                        var_dump($subjects);exit;

                        if (!empty($subjects)) {
                            foreach ($subjects as $subj) {
                                $tmpSubj = \common\helpers\Qa::formatSubjectFromGPT($subj);
                                $tmpSubj = \common\helpers\Qa::generateChallengePropByLevel($level, $tmpSubj);
                                $ret[] = $tmpSubj;
                            }
                        }

                    }
                } else {
                    $qaProp = [];
                }
                break;
        }

        return $ret;
    }

    public function getQaByPackageIds($qaPackageIds) {
        $qaPackages = QaPackage::find()
            ->where([
                'id' => $qaPackageIds
            ])
            ->all();

        $qaCollections = [];
        if (!empty($qaPackages)) {
            foreach ($qaPackages as $qaPackage) {
                $qaIds = explode(',', $qaPackage->qa_ids);

                if (!empty($qaIds)) {
                    $qaCollections[$qaPackage->id] = Qa::find()
                        ->where([
                            'id' => $qaIds
                        ])
                        ->all();
                }
            }
        }

        return $qaCollections;
    }

    public function generatePoem($level = 1, $poemType = 0, $poemClass = 0, $poemClass2 = 0, $answerType = Poem::POEM_ANSWER_TYPE_WORD) {
        $prop = [
            'poem_class' => $poemClass,
            'poem_class2' => $poemClass2,
        ];
        $poem = $this->getPoemByRand($poemType, $prop, $answerType);

        $hitRange = [
            5 * (1 + ($level - 1) / 5),
            10 * (1 + ($level - 1) / 5),
        ];
        $gold = 10 * (1 + ($level - 1) / 2);

        $showFormula = $poem['formula'];
        $answer = $poem['stAnswer'];
        $formula = $poem['poem'];
        $answerRange = $poem['selections'];

        $subjects = [
            'formula' => $showFormula,
            'topic' => $showFormula,
            'answer' => $answer,
            'st_answer' => $answer,
            'standFormula' => $formula,
            'answerRange' => $answerRange,
            'selected' => json_encode($answerRange, JSON_UNESCAPED_UNICODE),
            'selected_json' => $answerRange,
            'level' => $level,
            'hitRange' => $hitRange,
            'gold'  => $gold,
        ];

        return $subjects;
    }

    public function generateMath($level = 1, $gold = 0) {
        $subjects = [];
        switch ($level) {
            case 1:
                $subjects = $this->randMathFormula(2, 20, ['+','-'], $level, 1, $gold);
                break;
            case 2:
                $randNumCt = rand(2,3);
                $numMax= 20;
                if ($randNumCt == 2) {
                    $numMax = 100;
                }
                $subjects = $this->randMathFormula($randNumCt, $numMax, ['+','-'], $level, 2, $gold);
                break;
            case 3:
                $randNumCt = rand(2,3);
                $numMax= 100;
                $computeTag = ['+','-',];
                $mode = 2;
                if ($randNumCt == 2) {
                    $numMax = 10;
                    $computeTag = ['*'];
                    $mode = 1;
                }
                $subjects = $this->randMathFormula($randNumCt, $numMax, $computeTag, $level, $mode, $gold);
                break;
            case 4:
                $randNumCt = rand(2,3);
                $numMax= 100;
                $computeTag = ['+','-','*'];
                $mode = 2;
                if ($randNumCt == 2) {
                    $numMax = 10;
                    $computeTag = ['*','/'];
                    $mode = 1;
                }
                $subjects = $this->randMathFormula($randNumCt, $numMax, $computeTag, $level, $mode, $gold);
                break;
        }
        return $subjects;
    }

    public function getPoemById($poemId, $qaProp = [], $answerType = 0, $poemType, $ts = 0, $qaType = Qa::QA_TYPE_VERIFYCODE, $qaSelected = []) {
        if ($ts > 0) {
            srand($ts);
        }

        $poem = Poem::find()
            ->where([
                'id' => $poemId,
            ])
            ->one();

        if (empty($poem)) {
            return [];
        }

        $ret = $this->getPoemSubject($poem, $qaProp, $answerType);


        return $ret;
    }

    public function getPoemByRand($poemType = 0, $qaProp = [], $answerType = 0, $ts = 0, $qaType = Qa::QA_TYPE_VERIFYCODE, $qaSelected = []) {
        if ($ts > 0) {
            srand($ts);
        }

        $poem = Poem::find();
        if (!empty($poemType)) {
            $poem = $poem->andFilterWhere([
                'poem_type' => $poemType,
            ]);
        }
        if (!empty($qaProp['poem_class'])) {
            $poem = $poem->andFilterWhere([
                'poem_class' => $qaProp['poem_class'],
            ]);
        }
        if (!empty($qaProp['poem_class2'])) {
            $poem = $poem->andFilterWhere([
                'poem_class2' => $qaProp['poem_class2'],
            ]);
        }
        if (!empty($qaProp['level'])) {
            $poem = $poem->andFilterWhere([
                'level' => $qaProp['level'],
            ]);
        }
        if ($answerType == Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE) {
            $poem = $poem->andWhere([
                '<>', 'image', '',
            ]);
        }
        if ($answerType == Poem::POEM_ANSWER_TYPE_TITLE_FROM_STORY) {
            $poem = $poem->andWhere([
                '<>', 'story', '',
            ]);
        }

        $poem = $poem->orderBy('rand()')
            ->limit(1)
            ->one();

        if (empty($poem)) {
            return [];
        }

        $ret = $this->getPoemSubject($poem, $qaProp, $answerType);

        return $ret;
    }

    public function getPoemSubject($poem, $qaProp = [], $answerType = 0) {
        if (!empty($qaProp)) {
            if (empty($answerType)) {
                $answerType = !empty($qaProp['answer_type']) ? $qaProp['answer_type'] : 1;
            }
            $hole = !empty($qaProp['hole']) ? $qaProp['hole'] : 1;
        } else {
            $answerType = empty($answerType) ? Poem::POEM_ANSWER_TYPE_WORD : $answerType;
            $hole = 1;
        }

        $sentence = '';
        $retTemp = '';
        $answer = [];
//        $content = '';
        $content = $poem->content;
        switch ($answerType) {
            case Poem::POEM_ANSWER_TYPE_SENTENCE:
                // 猜上下句
                $ret = $this->getSentence($content, $hole, 0, $poem);
                break;
            case Poem::POEM_ANSWER_TYPE_TITLE:
            case Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE:
            case Poem::POEM_ANSWER_TYPE_TITLE_FROM_STORY:
            case Poem::POEM_ANSWER_TYPE_AUTHOR:
                // 猜标题 or 作者
                $ret = $this->getPoemTitle($poem, $answerType);
                break;
            case Poem::POEM_ANSWER_TYPE_WORD:
            default:
                // 填字
                $ret = $this->getWordFromSentence($content, $hole, 4, 0, $poem);
                break;

        }
        return $ret;
    }

    /*
     * 获取一个文字的相似文字
     */
    public function getSimilarWord($word, $ct = 3) {

//        $converter = new Chinese2pinyin();
//        $converter->transformWithoutTone($word, '', false);
//        return $ret;
        $pinyin = Yii::$app->chinesePinyin->transformWithoutTone($word, '', false);
        $wordList = Yii::$app->chinesePinyin->getWordFromPinyinWithoutTone($pinyin);

        for ($i=0; $i<$ct; $i++) {
            $ret[] = $wordList['word'][$pinyin][array_rand($wordList['word'][$pinyin])];
        }

        return $ret;

//        exit;
    }

    public function getSimilarTitleFromPoem($ct = 3, $answerType = 0, $rightPoem = null) {
        $poems = $this->getSimilarPoems($ct, $rightPoem);

        $ret = [];

        if (!empty($poems)) {
            foreach ($poems as $poem) {
                $ret[] = $poem->title;
            }
        }

        return $ret;
    }

    public function getSimilarSentenceFromPoem($ct = 3, $rightPoem = null) {
        $poems = $this->getSimilarPoems($ct, $rightPoem);

        $ret = [];

        if (!empty($poems)) {
            foreach ($poems as $poem) {
                $content = $poem->content;
                preg_match_all('/(.*?)([，。？]+)/u', $content, $matches);
                $sentRandom = array_rand($matches[1]);
                $content = $matches[1][$sentRandom];
                $ret[] = $content;
            }
        }

        return $ret;
    }

    public function getSimlarWordFromPoem($word, $ct = 3, $rightPoem = null) {

        // Todo：确定固定180首诗，随机抽取
        // 用代码的好处是，每次随机结果固定（srand设置）
//        for ($i=0; $i<$ct; $i++) {
//            $poemIds[] = rand(1,180);
//        }

        $poems = $this->getSimilarPoems($ct, $rightPoem);

//        $poems = Poem::find()
//            ->where([
//                'id' => $poemIds,
//            ])
//            ->all();

//        $pinyin = Yii::$app->chinesePinyin->transformWithoutTone($word, '', false);
//        $wordList = Yii::$app->chinesePinyin->getWordFromPinyinWithoutTone($pinyin);

        $ret = [];

        if (!empty($poems)) {
            foreach ($poems as $poem) {
                $content = $poem->content;
                $content = preg_replace('/([。？，《》 ]+)/u', '', $content);
                $ret[] = mb_substr($content, rand(0, mb_strlen($content, 'utf-8') - 1), 1, 'utf-8');
            }
        }

        return $ret;
    }

    public function getPoemTitle($poem, $answerType = 0) {
        if ($answerType == 0) {
//            $chosen = rand(1, 2);
            $randAnswerType = [
                Poem::POEM_ANSWER_TYPE_TITLE,
                Poem::POEM_ANSWER_TYPE_AUTHOR,
                Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE,
                Poem::POEM_ANSWER_TYPE_TITLE_FROM_STORY,
            ];
            $answerTypeIdx = array_rand($randAnswerType);
            $answerType = $randAnswerType[$answerTypeIdx];
        }

        // 1. 猜标题
        $content = $poem->content;
        $content = preg_replace('/([。？]+)/u', '${1}<br>', $content);
        switch ($answerType) {
            case Poem::POEM_ANSWER_TYPE_AUTHOR:
                // 2. 猜作者
    //            $retTemp = $poem->author;
                $answer[] = [
                    'author' => $poem->author,
                ];
                $stAnswer = $poem->author;
                $retTemp = $content;
                break;
            case Poem::POEM_ANSWER_TYPE_TITLE_FROM_IMAGE:
                // 3. 看图片猜名字
                $answer[] = [
                    'title' => $poem->title,
                ];
                $stAnswer = $poem->title;
                $retTemp = Attachment::completeUrl($poem->image, true);
                break;
            case Poem::POEM_ANSWER_TYPE_TITLE_FROM_STORY:
                // 4. 看故事猜名字
                $answer[] = [
                    'title' => $poem->title,
                ];
                $stAnswer = $poem->title;
                $retTemp = str_replace("\n", "<br>", $poem->story);
                break;
            case Poem::POEM_ANSWER_TYPE_TITLE:
            default:
                //            $retTemp = $poem->title;
                $answer[] = [
                    'title' => $poem->title,
                ];
                $stAnswer = $poem->title;
                //            $retTemp = str_replace($poem->title, '(?)', $retTemp);
                $retTemp = $content;
                break;
        }

        $finalCollections = $this->getSimilarTitleFromPoem(3, $answerType, $poem);
        $finalCollections[] = $stAnswer;

        shuffle($finalCollections);

        $ret = [
            'formula' => $retTemp,
            'topic' => $retTemp,
            'answer' => $answer,
            'st_answer' => $stAnswer,
            'stAnswer' => $stAnswer,
            'poem' => $poem->content,
            'data' => $poem,
            'selections' => $finalCollections,
            'selected_json' => $finalCollections,
            'selected' => json_encode($finalCollections, JSON_UNESCAPED_UNICODE),
        ];

        return $ret;
    }

    public function getSentence($content, $hole = 1, $ts = 0, $rightPoem = null) {
//        if ($ts > 0) {
//            srand($ts);
//        }
        $retTemp = '';
        $answer = [];
        $sentence = '';

        preg_match_all('/(.*?)([。！？；]+)/u', $content, $matches);
        if (!empty($matches[1])) {
            $retTempIdx = array_rand($matches[1]);
            $sentence = $retTemp = $matches[1][$retTempIdx] . $matches[2][$retTempIdx];

//            var_dump($retTemp);

            $sentArray = explode('，', $sentence);

            $answerIdx = array_rand($sentArray);

            $similarCollections = $this->getSimilarSentenceFromPoem(3, $rightPoem);

            $answer[$answerIdx] = [
                'i' => $answerIdx,
                'sentence' => $sentArray[$answerIdx],
                'similar' => $similarCollections
            ];

            $stAnswer = $sentArray[$answerIdx];

            $finalCollections = $similarCollections;
            $finalCollections[] = $sentArray[$answerIdx];
            shuffle($finalCollections);

            $retTemp = str_replace($sentArray[$answerIdx], '(?)', $retTemp);

        }

        $ret = [
            'formula' => $retTemp,
            'topic' => $retTemp,
            'answer' => $answer,
            'st_answer' => $stAnswer,
            'stAnswer' => $stAnswer,
            'poem' => $content,
            'sentence' => $sentence,
            'selections' => $finalCollections,
            'selected_json' => $finalCollections,
            'selected' => json_encode($finalCollections, JSON_UNESCAPED_UNICODE),
        ];

        return $ret;


    }

    public function getWordFromSentence($content, $hole = 1, $resCt = 4, $ts = 0, $rightPoem = null) {

//        if ($ts > 0) {
//            srand($ts);
//        }

        $retTemp = '';
        $answer = [];
        $sentence = '';
        $finalCollection = [];

        $stAnswer = '';

        if (!empty($rightPoem) &&
            ($rightPoem->poem_type == Poem::POEM_TYPE_POEM
                || $rightPoem->poem_type == Poem::POEM_TYPE_POETRY
            )) {
            preg_match_all('/(.*?)([。？！；]+)/u', $content, $matches);
            if (!empty($matches[1])) {
                $retTempIdx = array_rand($matches[1]);

                $sentence = $retTemp = $matches[1][$retTempIdx] . $matches[2][$retTempIdx];
            }
        } else {
            $sentence = $retTemp = $content;
        }
            for ($i=0; $i < $hole; $i++) {

                $rndCt = rand(0, mb_strlen($retTemp, 'utf-8') - 2);
                $chosenWord = mb_substr($retTemp, $rndCt, 1, 'utf-8');
                if (in_array($chosenWord, ['。', '？', '，'])
                    || isset($answer[$rndCt])
                ) {
                    $i--;
                    continue;
                }


                $answer[$rndCt] = [
                    'i' => $rndCt,
                    'word' => $chosenWord,
//                    'similarPinyin' => $this->getSimilarWord($chosenWord),
                    'similar' => $this->getSimlarWordFromPoem($chosenWord, $resCt - 1, $rightPoem),
                ];
            }
            asort($answer);

            $resCollection = [];
            $tempCollection = [];

            $py = 0; // 偏移量
            $stAnswers = [];
            foreach ($answer as $i => $a) {
                $word = $a['word'];
//                $retTemp = str_replace($word, '(?)', $retTemp);
                $retTemp = \common\helpers\Common::mbSubStrReplace($retTemp, '(?)', $i + $py, 1);
                $py += 2;

                $ans = $a['word'];
                $stAnswers [] = $ans;
                $resCollection[] = $ans;
                $tempCollection += $a['similar'];
            }
            $stAnswer = implode(',', $stAnswers);

            $tempCollection = array_unique($tempCollection);
            $tempCollection = array_slice($tempCollection, 0, $resCt - count($resCollection));

//            var_dump($tempCollection);

            $finalCollection = array_merge($resCollection, $tempCollection);
            shuffle($finalCollection);


        $ret = [
            'formula' => $retTemp,
            'topic' => $retTemp,
            'answer' => $answer,
            'stAnswer' => $stAnswer,
            'st_answer' => $stAnswer,
            'poem' => $content,
            'sentence' => $sentence,
            'selections' => $finalCollection,
            'selected_json' => $finalCollection,
            'selected' => json_encode($finalCollection, JSON_UNESCAPED_UNICODE),
        ];

        return $ret;
    }

    public function getSimilarPoems($ct, $rightPoem = null) {
        if (empty($rightPoem)) {
            for ($i = 0; $i < $ct; $i++) {
                $poemIds[] = rand(1, 180);
            }

            $poems = Poem::find()
                ->where([
                    'id' => $poemIds,
                ])
                ->all();
        } else {
            $rightPoemType = $rightPoem->poem_type;
            $rightPoemLevel = $rightPoem->level;
            $rightPoemClass = $rightPoem->poem_class;
            $rightPoemClass2 = $rightPoem->poem_class2;
            $rightPoemId = $rightPoem->id;

            $poems = Poem::find();
            if (!empty($rightPoemType)) {
                $poems = $poems->andFilterWhere([
                    'poem_type' => $rightPoemType,
                ]);
            }
            if (!empty($rightPoemLevel)) {
                $poems = $poems->andFilterWhere([
                    'level' => $rightPoemLevel,
                ]);
            }
            if (!empty($rightPoemClass)) {
                $poems = $poems->andFilterWhere([
                    'poem_class' => $rightPoemClass,
                ]);
            }
            if (!empty($rightPoemClass2)) {
                $poems = $poems->andFilterWhere([
                    'poem_class2' => $rightPoemClass2,
                ]);
            }
            if (!empty($rightPoemId)) {
                $poems = $poems->andFilterWhere([
                    '<>', 'id', $rightPoemId,
                ]);
            }
            $poems = $poems->orderBy('rand()')
                ->limit($ct)
                ->all();
        }

        return $poems;
    }


    public function randMathFormula($numCt = 2, $numMax = 20, $opRange = ['+','-','*','/'], $level, $mode = 1, $gold = 0){

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

        if (empty($gold)) {
            $gold = 10 * (1 + ($level - 1) / 2);
        }

        return [
            'formula' => $showFormula,
            'topic'     => $showFormula,
            'st_answer' => $answer,
            'answer' => $answer,
            'standFormula' => $formula,
            'answerRange' => $answerRange,
            'selected_json' => $answerRange,
            'selected' => json_encode($answerRange, JSON_UNESCAPED_UNICODE),
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

}