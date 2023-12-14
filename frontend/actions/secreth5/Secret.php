<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\secreth5;


use common\definitions\Common;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Secret extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $pinCode = !empty($_GET['pin_code']) ? $_GET['pin_code'] : 0;

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
            $qaOne['attachment'] = \common\helpers\Attachment::completeUrl($qaOne['attachment'], true);

        }

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