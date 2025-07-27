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
use common\models\UserEBook;
use common\models\UserExtends;
use common\models\UserKnowledge;
use yii\base\Action;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class SetEbookStory extends Action
{

    
    public function run()
    {
        $userId = !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;
        $ebookStory = UserEBook::find()
            ->where([
                'user_id' => $userId,
                'ebook_status' => [
                    UserEBook::USER_EBOOK_STATUS_DEFAULT,
                    UserEBook::USER_EBOOK_STATUS_PLAYING
                ]
            ])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->one();

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
//            $userId = !empty($_POST['user_id']) ? $_POST['user_id'] : 0;

            if (!empty($ebookStory) && $ebookStory->ebook_status == UserEBook::USER_EBOOK_STATUS_PLAYING) {
                return $this->controller->render('set_ebook_story', [
                    'params'        => $_REQUEST,
                    'userId'        => $userId,
                    'message' => '当前剧本已经开启，无法更改',
                ]);
            }

            $userEbookStoryId = !empty($_POST['user_ebook_story_id']) ? $_POST['user_ebook_story_id'] : 0;

            if (empty($ebookStory)) {
                $ebookStory = new UserEBook();
                $ebookStory->user_id = $userId;
            }
            $ebookStory->ebook_story = $userEbookStoryId;
            $ebookStory->ebook_story_params = json_encode(!empty(UserEBook::$poiList[$userEbookStoryId]) ? UserEBook::$poiList[$userEbookStoryId] : [], JSON_UNESCAPED_UNICODE);

            $ret = $ebookStory->save();
            if (!$ret) {
                return $this->controller->render('set_ebook_story', [
                    'params'        => $_REQUEST,
                    'userId'        => $userId,
                    'message' => '设置失败，请稍后再试',
                ]);
            }

            return $this->controller->render('set_ebook_story', [
                'params'        => $_REQUEST,
                'userId'        => $userId,
                'message' => '设置成功',
            ]);
        }


        $ebookStoryId = 0;
        if (!empty($ebookStory)) {
            $ebookStoryId = $ebookStory->ebook_story;
        }

        $ebookStoryList = UserEBook::$poiList;


        return $this->controller->render('set_ebook_story', [
            'params'        => $_REQUEST,
            'userId'        => $userId,
            'ebook_story_id'    => $ebookStoryId,
            'ebook_story_list'   => $ebookStoryList,
        ]);
    }
}