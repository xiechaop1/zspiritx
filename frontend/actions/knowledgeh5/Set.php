<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\knowledgeh5;


use common\definitions\Common;
use common\models\Story;
use common\models\UserKnowledge;
use common\models\UserModels;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Set extends Action
{

    
    public function run()
    {
        $knowledgeId = !empty($_GET['knowledge_id']) ? $_GET['knowledge_id'] : 0;
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;

        $knowledgeClassId = !empty($_GET['knowledge_class_id']) ? $_GET['knowledge_class_id'] : \common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL;
        $act = !empty($_GET['act']) ? $_GET['act'] : 'completed';

        $knowledge = \common\models\Knowledge::find()
            ->where(['id' => $knowledgeId])
            ->one();

        $userKnowledge = Yii::$app->knowledge->get($knowledgeId, $sessionId, $userId);

        if (empty($userKnowledge)) {
            $userKnowledge = Yii::$app->knowledge->set($knowledgeId, $sessionId, $sessionStageId, $userId, $storyId, $act);
        }

        if ($knowledge->knowledge_class == \common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL) {
            $msg = '您成功获得了知识 ' . $knowledge->title . '，可以到"我的"->"知识"中查看';
        } else {
            if ($act == 'completed') {
                $actionTxt = '您已经完成了任务 ';
            }  else {
                $actionTxt = '您正在进行任务 ';
            }
            $msg = $actionTxt . $knowledge->title . '，可以到"我的"->"任务"中查看';
        }

        return $this->controller->render('set', [
            'userKnowledge' => $userKnowledge,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'knowledgeClass' => $knowledgeClassId,
            'msg'           => $msg,
        ]);
    }
}