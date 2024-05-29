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
use common\models\Poem;
use common\models\Qa;
use common\models\Session;
use common\models\UserKnowledge;
use common\models\UserQa;
use common\models\UserScore;
use liyifei\chinese2pinyin\Chinese2pinyin;
use yii\base\Component;
use yii;

class Qas extends Component
{
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

        $poem = Poem::find()
            ->orderBy('rand()')
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
            $answerType = empty($answerType) ? 1 : $answerType;
            $hole = 1;
        }

        $sentence = '';
        $retTemp = '';
        $answer = [];
//        $content = '';
        $content = $poem->content;
        if ($answerType == 1) {
            // 填字
            $ret = $this->getWordFromSentence($content, $hole);
        } elseif ($answerType == 2) {
            // 猜上下句
            $ret = $this->getSentence($content, $hole);
        } elseif ($answerType == 3) {
            // 猜标题/作者
            $ret = $this->getPoemTitle($poem);
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

    public function getSimilarTitleFromPoem($ct = 3) {
        for ($i=0; $i<$ct; $i++) {
            $poemIds[] = rand(1,180);
        }

        $poems = Poem::find()
            ->where([
                'id' => $poemIds,
            ])
            ->all();

        $ret = [];

        if (!empty($poems)) {
            foreach ($poems as $poem) {
                $ret[] = $poem->title;
            }
        }

        return $ret;
    }

    public function getSimilarSentenceFromPoem($ct = 3) {
        for ($i=0; $i<$ct; $i++) {
            $poemIds[] = rand(1,180);
        }

        $poems = Poem::find()
            ->where([
                'id' => $poemIds,
            ])
            ->all();

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

    public function getSimlarWordFromPoem($word, $ct = 3) {

        // Todo：确定固定180首诗，随机抽取
        // 用代码的好处是，每次随机结果固定（srand设置）
        for ($i=0; $i<$ct; $i++) {
            $poemIds[] = rand(1,180);
        }

//        $poems = Poem::find()
//            ->orderBy('rand()')
//            ->limit($ct)
//            ->all();

        $poems = Poem::find()
            ->where([
                'id' => $poemIds,
            ])
            ->all();

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

    public function getPoemTitle($poem) {
        $chosen = rand(1,2);

        // 1. 猜标题
        $content = $poem->content;
        $content = preg_replace('/([。？]+)/u', '${1}<br>', $content);if ($chosen == 1) {
            $retTemp = $poem->title;
            $answer[] = [
                'title' => $poem->title,
            ];
            $stAnswer = $poem->title;
//            $retTemp = str_replace($poem->title, '(?)', $retTemp);
            $retTemp = $content;
        } else {
            // 2. 猜作者
            $retTemp = $poem->author;
            $answer[] = [
                'author' => $poem->author,
            ];
            $stAnswer = $poem->author;
            $retTemp = $content;
        }

        $finalCollections = $this->getSimilarTitleFromPoem(3);
        $finalCollections[] = $stAnswer;

        shuffle($finalCollections);

        $ret = [
            'formula' => $retTemp,
            'answer' => $answer,
            'stAnswer' => $stAnswer,
            'poem' => $poem->content,
            'selections' => $finalCollections,
        ];

        return $ret;
    }

    public function getSentence($content, $hole = 1, $ts = 0) {
//        if ($ts > 0) {
//            srand($ts);
//        }
        $retTemp = '';
        $answer = [];
        $sentence = '';

        preg_match_all('/(.*?)([。？]+)/u', $content, $matches);
        if (!empty($matches[1])) {
            $retTempIdx = array_rand($matches[1]);
            $sentence = $retTemp = $matches[1][$retTempIdx] . $matches[2][$retTempIdx];

            $sentArray = explode('，', $sentence);

            $answerIdx = array_rand($sentArray);

            $similarCollections = $this->getSimilarSentenceFromPoem(3);

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
            'answer' => $answer,
            'stAnswer' => $stAnswer,
            'poem' => $content,
            'sentence' => $sentence,
            'selections' => $finalCollections,
        ];

        return $ret;


    }

    public function getWordFromSentence($content, $hole = 1, $resCt = 4, $ts = 0) {

//        if ($ts > 0) {
//            srand($ts);
//        }

        $retTemp = '';
        $answer = [];
        $sentence = '';
        $finalCollection = [];

        preg_match_all('/(.*?)([。？]+)/u', $content, $matches);
        if (!empty($matches[1])) {
            $retTempIdx = array_rand($matches[1]);

            $sentence = $retTemp = $matches[1][$retTempIdx] . $matches[2][$retTempIdx];
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
                    'similar' => $this->getSimlarWordFromPoem($chosenWord, $resCt - 1),
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
        }

        $ret = [
            'formula' => $retTemp,
            'answer' => $answer,
            'stAnswer' => $stAnswer,
            'poem' => $content,
            'sentence' => $sentence,
            'selections' => $finalCollection,
        ];

        return $ret;
    }


}