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
use common\models\UserExtends;
use common\models\UserKnowledge;
use yii\base\Action;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class Settings extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;

        $userExtends = UserExtends::find()
            ->where(['user_id' => $userId])
            ->one();

        $user = User::find()
            ->where(['id' => $userId])
            ->one();

        if (!empty($_GET['grade'])) {
            if (empty($userExtends)) {
                $userExtends = new UserExtends();
                $userExtends->user_id = $userId;
            }
            $userExtends->load(['UserExtends' => Yii::$app->request->get()]);
//            var_dump($userExtends);exit;
            if (!empty($userExtends->level)) {
                if (!empty(UserExtends::$userGradeLevelMap[$userExtends->grade])
//                    && UserExtends::$userGradeLevelMap[$userExtends->grade] > $userExtends->level
                ) {
                    $userExtends->level = UserExtends::$userGradeLevelMap[$userExtends->grade];
                }
            } else {
                $userExtends->level = UserExtends::$userGradeLevelMap[$userExtends->grade];
            }
            $ret = $userExtends->save();

            if ($ret) {
                return $this->controller->redirect(['myh5/settings', 'user_id' => $userId]);
            }
        }

//        var_dump($userKnowledge);exit;

        return $this->controller->render('settings', [
            'params'        => $_GET,
            'userId'        => $userId,
            'user'          => $user,
            'sessionId'     => $sessionId,
            'storyId'       => $storyId,
            'userExtends'   => $userExtends,
        ]);
    }
}