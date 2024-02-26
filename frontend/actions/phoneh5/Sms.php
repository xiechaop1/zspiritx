<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\phoneh5;


use common\definitions\Common;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Sms extends Action
{

    
    public function run()
    {
        $get = \Yii::$app->request->get();
        $userId = !empty($get['user_id']) ? $get['user_id'] : 0;
        $sessionId = !empty($get['session_id']) ? $get['session_id'] : 0;
        $teamId = !empty($get['team_id']) ? $get['team_id'] : 0;
        $qaId = !empty($get['qa_id']) ? $get['qa_id'] : 0;
        $userLng = !empty($get['user_lng']) ? $get['user_lng'] : 0;
        $userLat = !empty($get['user_lat']) ? $get['user_lat'] : 0;
        $storyStageId = !empty($get['story_stage_id']) ? $get['story_stage_id'] : 0;
        $storyId = !empty($get['story_id']) ? $get['story_id'] : 0;
        $passwd = !empty($get['passwd']) ? $get['passwd'] : '';
        $forgot = !empty($get['forgot']) ? $get['forgot'] : 0;
        $disRange = 2000;

        $qa = Qa::findOne($qaId);

        $label = '信息';
        $label2 = '安全验证';
        $selectedArr = [];
        $smsContents = [];

        if (!empty($qa)) {
            $selectedJson = $qa['selected'];
            $selectedArr = json_decode($selectedJson, true);

            $qa['selected'] = $selectedArr;

            $smsContents = !empty($selectedArr['sms_contents']) ? $selectedArr['sms_contents'] : [];

            $label = !empty($selectedArr['label']) ? $selectedArr['label'] : $label;
            $label2 = !empty($selectedArr['label2']) ? $selectedArr['label2'] : '安全验证';

            $returnAnswerType = !empty($selectedArr['return_answertype']) ? $selectedArr['return_answertype'] : 2;
        }

        return $this->controller->render('sms', [
            'userId'    => $userId,
            'sessionId' => $sessionId,
            'teamId'    => $teamId,
            'qaId'      => $qaId,
            'storyId'   => $storyId,
            'userLng'   => $userLng,
            'userLat'   => $userLat,
            'storyStageId' => $storyStageId,
            'disRange'  => $disRange,
            'label'     => $label,
            'label2'    => $label2,
            'forgot'    => $forgot,
            'passwd'    => $passwd,
            'qa'        => $qa,
            'smsContents' => $smsContents,
            'selectedArr' => $selectedArr,
            'returnAnswerType' => $returnAnswerType,
        ]);
    }
}