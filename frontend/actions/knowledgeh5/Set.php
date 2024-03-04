<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\knowledgeh5;


use common\models\UserKnowledge;
use yii\base\Action;

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
        $act = !empty($_GET['act']) ? $_GET['act'] : 'complete';

        if ($act == 'completed') {
            $act = 'complete';
        }

        $knowledge = \common\models\Knowledge::find()
            ->where(['id' => $knowledgeId])
            ->one();

        $userKnowledge = Yii::$app->knowledge->get($knowledgeId, $sessionId, $userId);

        switch ($act) {
            case 'complete':
                $knowledgeStatus = UserKnowledge::KNOWLDEGE_STATUS_COMPLETE;
                break;
            case 'process':
                $knowledgeStatus = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
                break;
            default:
                $knowledgeStatus = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
                break;
        }

        if (empty($userKnowledge)
        || ($userKnowledge->knowledge_status != $knowledgeStatus)
        ) {
            $userKnowledge = Yii::$app->knowledge->set($knowledgeId, $sessionId, $sessionStageId, $userId, $storyId, $act);
        }

        if ($knowledge->knowledge_class == \common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL) {
//            $msg = '您成功获得了知识 ' . $knowledge->title . '，可以到"我的"->"知识"中查看';
            $actionTxt = '您<span style="color: #80ff00; ">成功获得了知识</span> ';
            $actionTypeTxt = '知识';
        } else {
            if ($act == 'complete') {
                $actionTxt = '您已经<span style="color: #f84934">完成了任务</span> ';
            } else {
                $actionTxt = '您正在<span style="color: #80ff00; ">进行任务</span> ';
            }
            $actionTypeTxt = '任务';
        }
        $msg = $actionTxt . '<span style="color: yellow">' . $knowledge->title . '</span>';
//        if ($act != 'complete') {
//        $msg .= '<hr style="border: 0px solid #f1fa8c; ">';
        $msg .= '<br><br>';
        $msg .= '可以到<span style="color: yellow">"我的"->"' . $actionTypeTxt . '"</span>中查看';
            $msg .= '<br><a style="color:yellow;" href="/knowledgeh5/all?user_id=' . $userId . '&session_id=' . $sessionId . '&story_id=' . $storyId . '&knowledge_class_id=' . $knowledge->knowledge_class . '&show_knowledge_id=' . $knowledgeId . '">[查看任务]</a><br><br>';
//        }

        return $this->controller->render('set', [
            'userKnowledge' => $userKnowledge,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'knowledgeClass' => $knowledgeClassId,
            'act'           => $act,
            'msg'           => $msg,
            'knowledge'     => $knowledge,
        ]);
    }
}