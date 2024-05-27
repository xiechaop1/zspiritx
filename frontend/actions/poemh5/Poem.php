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

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

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
//                $selectedArray = json_decode($selectedJson, true);
                $poemId = !empty($selectedArray['poem_id']) ? $selectedArray['poem_id'] : 0;
                $poemRandom = !empty($selectedArray['poem_random']) ? $selectedArray['poem_random'] : 0;
                $poemType = !empty($selectedArray['poem_type']) ? $selectedArray['poem_type'] : 0;

                $poem = Yii::$app->qas->getPoemById($poemId, $poemType, $qaOne['qa_type'], $selectedArray, $qaOne['prop']);

                var_dump($poem);exit;


            }

        }
        exit;

        return $this->controller->render('secret', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'=> $sessionStageId,
            'qaId'          => $qaId,
            'storyId'       => $storyId,
            'pinCode'       => $pinCode,
            'rightAnswer'   => $qaOne['st_answer'],
            'stSelected'    => $qaOne['st_selected'],
            'qa'         => $qaOne,
        ]);
    }
}