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

class Knowledge extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $model = \common\models\Knowledge::find()
            ->where([
                'story_id'  => $storyId,
                'is_delete' => Common::STATUS_NORMAL,
            ])
            ->orderBy('sort_by ASC')
            ->all();

        $userKnowledge = UserKnowledge::find()
            ->where([
                'user_id'       => $userId,
                'session_id'    => $sessionId,
            ])
            ->all();

        $userKnowledgeMap = ArrayHelper::map($userKnowledge, 'knowledge_id', 'knowledge_status');


        return $this->controller->render('knowledge', [
            'model'         => $model,
            'userKnowledge' => $userKnowledge,
            'userKnowledgeMap' => $userKnowledgeMap,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
        ]);
    }
}