<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\jncityh5;


use common\models\UserEBook;
use yii\base\Action;

class UploadImage extends Action
{

    private $_get;

    private $_params;
    
    public function run()
    {
        $this->_get = Yii::$app->request->get();
//
//        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
//        $userLng = !empty($this->_get['user_lng']) ? $this->_get['user_lng'] : 0;
//        $userLat = !empty($this->_get['user_lat']) ? $this->_get['user_lat'] : 0;
//
//        $this->_params = [
//            'user_id'    => $userId,
//            'session_id'    => $sessionId,
//        ];
//
//        try {
//            $user = User::find()->where(['id' => $userId])->one();
//            if (empty($user->home_lng) || empty($user->home_lat)) {
//                $user->home_lng = $userLng;
//                $user->home_lat = $userLat;
//                $user->save();
//            }
//            $msg = '迁入了新家！你需要重启一下app，你的新家就完成最后一步啦！';
//        } catch (\Exception $e) {
//            return $this->renderErr($e->getMessage(), $this->_params);
////            return $this->fail($e->getMessage(), $e->getCode());
//        }
//
//        return $this->renderErr($msg, $this->_params);

        $userEBook = UserEBook::find()
            ->where(['user_id' => $userId])
            ->andFilterWhere([
                'ebook_status' => UserEBook::USER_EBOOK_STATUS_PLAYING
            ])
            ->orderBy([
                'created_at' => SORT_DESC,
            ])
            ->one();

        if (!empty($userEBook)) {
            $ebookStory = $userEBook->ebook_story;
        } else {
            $ebookStory = 1;
        }

        $poiList = !empty(UserEBook::$poiList[$ebookStory]) ? UserEBook::$poiList[$ebookStory]['pois'] : [];

        return $this->controller->render('uploadimage', [
            'userId' => $userId,
            'storyId' => $storyId,
            'poiList' => $poiList,
            'ebookStory' => $ebookStory,
        ]);

    }

    public function renderErr($errTxt, $params = []) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}