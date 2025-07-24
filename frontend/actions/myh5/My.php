<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\myh5;


use common\models\Knowledge;
use common\models\User;
use common\models\UserKnowledge;
use yii\base\Action;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class My extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $userKnowledges = UserKnowledge::find()
            ->where([
                'user_id'       => $userId,
                'session_id'    => $sessionId,
                'is_read'       => UserKnowledge::KNOWLEDGE_IS_READ_NO,
            ])
            ->all();

        $userKnowledge = [];
        foreach (Knowledge::$knowledgeClass2Name as $class => $className) {
            $userKnowledge[$class] = 0;
        }
        foreach ($userKnowledges as $uk) {
            $knowledge = $uk->knowledge;
            $userKnowledge[$knowledge->knowledge_class] = 1;
        }

        $user = User::find()
            ->where(['id' => $userId])
            ->one();

//        var_dump($userKnowledge);exit;

        $defStoryId = 5;

        $userIp = Yii::$app->request->userIP;
        $isHongKong = \common\helpers\Common::checkPosByIP($userIp, '香港');
        if ($isHongKong) {
            $defStoryId = 16;       //  坚尼地城的剧本ID
        }

        return $this->controller->render('my', [
            'params'        => $_GET,
            'userId'        => $userId,
            'user'          => $user,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'userKnowledge' => $userKnowledge,
            'defStoryId'    => $defStoryId,
        ]);
    }
}