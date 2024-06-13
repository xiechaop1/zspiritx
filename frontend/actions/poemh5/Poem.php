<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\poemh5;


use common\definitions\Common;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Poem extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $hole = !empty($_GET['hole']) ? $_GET['hole'] : 0;

        $ts = !empty($_GET['ts']) ? $_GET['ts'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

        $poemId = !empty($_GET['poem_id']) ? $_GET['poem_id'] : 0;
        $poemRandom = !empty($_GET['poem_random']) ? $_GET['poem_random'] : 0;
        $poemType = !empty($_GET['poem_type']) ? $_GET['poem_type'] : 0;
        $answerType = !empty($_GET['answer_type']) ? $_GET['answer_type'] : 0;
        $poem = [];

        if (!empty($qaId)) {
            $qaOne = Qa::find()
                ->where([
                    'id'    => $qaId,
                ])
                ->one();

            if (empty($qaOne)) {
                throw new NotFoundHttpException('QA not found');
            }
            $qaOne = $qaOne->toArray();
            $qaOne['selected_json'] = \common\helpers\Common::isJson($qaOne['selected']) ? json_decode($qaOne['selected'], true) : $qaOne['selected'];
            $qaOne['prop'] = \common\helpers\Common::isJson($qaOne['prop']) ? json_decode($qaOne['prop'], true) : $qaOne['prop'];
            $qaOne['attachment'] = \common\helpers\Attachment::completeUrl($qaOne['attachment'], true);

            if (!empty($qaOne['selected_json'])) {
                $selectedArray = $qaOne['selected_json'];
                $propArray = $qaOne['prop'];
                if (!empty($hole)) {
                    $propArray['hole'] = $hole;
                }
//                $selectedArray = json_decode($selectedJson, true);
//                $poemId = !empty($propArray['poem_id']) ? $propArray['poem_id'] : 0;
                if (empty($poemId) && !empty($propArray['poem_id'])) {
                    $poemId = $propArray['poem_id'];
                }
                if (empty($poemRandom) && !empty($propArray['poem_random'])) {
                    $poemRandom = $propArray['poem_random'];
                }
                if (empty($poemType) && !empty($propArray['poem_type'])) {
                    $poemType = $propArray['poem_type'];
                }

                if (empty($answerType) && !empty($propArray['answer_type'])) {
                    $answerType = $propArray['answer_type'];
                }
//                $poemId = empty($poemId) ? $propArray['poem_id'] : $poemId;
//                $poemRandom = empty($poemRandom) ? $propArray['poem_random'] : $poemRandom;
//                $poemType = empty($poemType) ? $propArray['poem_type'] : $poemType;
                if (!empty($poemId)) {
                    $poem = Yii::$app->qas->getPoemById($poemId, $propArray, $answerType, $poemType, $ts, $qaOne['qa_type'], $selectedArray);
                } else {
                    $poem = Yii::$app->qas->getPoemByRand($poemType, $propArray, $answerType, $ts, $qaOne['qa_type'], $selectedArray);
                }


                if (empty($poem)) {
                    throw new NotFoundHttpException('Poem not found');
                }
//                var_dump($poem);exit;


            }

        }
//        exit;

        return $this->controller->render('poem', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'=> $sessionStageId,
            'qaId'          => $qaId,
            'poemId'        => $poemId,
            'poem'          => $poem,
            'storyId'       => $storyId,
            'rtnAnswerType'    => 2,
            'stAnswer' => !empty($poem['stAnswer']) ? $poem['stAnswer'] : '',
//            'stAnswer'   => $qaOne['st_answer'],
            'stSelected'    => $qaOne['st_selected'],
            'qa'         => $qaOne,
        ]);
    }
}