<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\visual;


use common\definitions\Common;
use common\models\UserKnowledge;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Web extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

//        $knowledgeClassId = !empty($_GET['knowledge_class_id']) ? $_GET['knowledge_class_id'] : \common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL;
//
//        $showKnowledgeId = !empty($_GET['show_knowledge_id']) ? $_GET['show_knowledge_id'] : 0;
//
//        $model = \common\models\Knowledge::find()
//            ->where([
//                'knowledge_class' => $knowledgeClassId,
//                'story_id'  => $storyId,
//                'is_delete' => Common::STATUS_NORMAL,
//            ])
//            ->orderBy('sort_by ASC')
//            ->all();
//
//        $userKnowledges = UserKnowledge::find()
//            ->where([
//                'user_id'       => $userId,
//                'session_id'    => $sessionId,
//            ])
//            ->all();
//
//        $userKnowledgeMap = ArrayHelper::map($userKnowledges, 'knowledge_id', 'knowledge_status');
//
//        $userKnowledge = [];
//        foreach ($userKnowledges as $uk) {
//            $userKnowledge[$uk->knowledge_id] = $uk;
//        }


        return $this->controller->render('web', [
//            'model'         => $model,
//            'userKnowledge' => $userKnowledge,
//            'userKnowledgeMap' => $userKnowledgeMap,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
//            'knowledgeClass' => $knowledgeClassId,
//            'showKnowledgeId' => $showKnowledgeId,
        ]);
    }
}