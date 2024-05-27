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
use yii\base\Component;
use yii;

class Qas extends Component
{
    public function getPoemById($poemId, $poemType, $qaType = Qa::QA_TYPE_VERIFYCODE, $qaSelected = [], $qaProp = []) {
        $poem = Poem::find()
            ->where([
                'id' => $poemId,
            ])
            ->one();

        if (empty($poem)) {
            return [];
        }

        if (!empty($qaProp)) {
            $answerType = !empty($qaSelected['answer_type']) ? $qaSelected['answer_type'] : 1;
            $hole = !empty($qaSelected['hole']) ? $qaSelected['hole'] : 1;
        } else {
            $answerType = 1;
            $hole = 1;
        }

        if ($answerType == 1) {
            $content = $poem->content;

            preg_match_all($content, '/(.*?)([。？!]+)/', $matches);

            var_dump($matches);

            if (!empty($matches[1])) {
                $retTempIdx = array_rand($matches[1]);

                $retTemp = $matches[1][$retTempIdx] . $matches[2][$retTempIdx];

                for ($i=0; $i < $hole; $i++) {
                    $rndCt = rand(0, mb_strlen($retTemp, 'utf-8') - 1);
                    $answer[] = mb_substr($retTemp, $rndCt, 1, 'utf-8');
                    $retTemp = mb_substr($retTemp, 0, $rndCt, 'utf-8') . '(?)' . mb_substr($retTemp, $rndCt + 1, null, 'utf-8');
                }
            }
        }




        return $poem;
    }


}