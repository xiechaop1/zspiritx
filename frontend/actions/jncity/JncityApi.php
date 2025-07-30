<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\jncity;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Knowledge;
use common\models\Qa;
use common\models\SessionQa;
use common\models\StoryMatch;
use common\models\StoryStages;
use common\models\UserBook;
use common\models\UserData;
use common\models\UserEBook;
use common\models\UserKnowledge;
use common\models\UserQa;
use common\models\User;
//use liyifei\base\actions\ApiAction;
use common\models\UserList;
use common\models\UserScore;
use common\models\UserStory;
use frontend\actions\ApiAction;
use yii;

class JncityApi extends ApiAction
{
    public $action;

//    public $userId;

    private $_storyId;

    private $_story;

    private $_get;

    public function run()
    {
        $this->_get = Yii::$app->request->get();

        try {
            $this->_storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

            switch ($this->action) {
                case 'poi_list':
                    $ret = $this->poiList();
                    break;
                case 'story_list':
                    $ret = $this->storyList();
                    break;
                case 'change_story':
                    $ret = $this->changeStory();
                    break;
                case 'get_story':
                    $ret = $this->getStory();
                    break;
                case 'get_user_ebook':
                    $ret = $this->getUserEbook();
                    break;
                case 'get_user_one_ebook':
                    $ret = $this->getUserOneEbook();
                    break;
                case 'get_user_last_ebook':
                    $ret = $this->getUserLastEbook();
                    break;
                case 'upload':
                    $ret = $this->upload();
                    break;
                case 'generate_video':
                    $ret = $this->generateVideo();
                    break;
                default:
                    $ret = [];
                    break;

            }
        } catch (\Exception $e) {
            return $this->fail($e->getCode() . ': ' . $e->getMessage());
        }

        return $this->success($ret);
    }

    // 获取问答列表
    public function poiList() {
        $poiList = UserEBook::$poiList;

        return $poiList;

    }

    public function storyList() {
        $storyList = UserEBook::$poiList;

        if (empty($storyList)) {
            return [];
        }

//        foreach ($storyList as &$story) {
//            $story['poi'] = !empty(UserEBook::$poiList[$story['poi']])
//                ? UserEBook::$poiList[$story['poi']] : [];
//        }

        return $storyList;
    }

    public function changeStory() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $ebookStory = !empty($this->_get['ebook_story']) ? $this->_get['ebook_story'] : '';

        $userEbook = UserEBook::find()
            ->where([
                'user_id' => $userId,
            ])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->one();

        if (!empty($userEbook)) {
            if ($userEbook->ebook_status == UserEBook::USER_EBOOK_STATUS_PLAYING) {
                throw new \Exception('当前的电子书已经进行中，不能修改故事', ErrorCode::EBOOK_USER_EBOOK_STATUS_PLAYING);
            } else if ($userEbook->ebook_status == UserEBook::USER_EBOOK_STATUS_COMPLETED) {
                throw new \Exception('当前电子书已完成，请重新开始新的电子书', ErrorCode::EBOOK_USER_EBOOK_STATUS_COMPLETED);
            }

            $ebookParam = !empty(UserEBook::$poiList[$ebookStory])
                ? UserEBook::$poiList[$ebookStory] : [];

            if (empty($ebookParam)) {
                return false;
            }

            $storyId = 100;

            $userEbook->ebook_story = $ebookStory;
            $userEbook->ebook_story_params = $ebookParam;
            $r = $userEbook->save();

            return $userEbook;

        } else {
            throw new \Exception('电子书尚未开启', ErrorCode::EBOOK_USER_EBOOK_STATUS_NONE);
        }
    }

    public function getUserEbook() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

//        if (empty($userId)) {
//            throw new \Exception('用户ID不能为空', ErrorCode::EBOOK_USER_ID_EMPTY);
//        }

        $userEbook = UserEBook::find()
            ->where(['user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $ret = [];

        if (!empty($userEbook)) {
            foreach ($userEbook as $ue) {
                $tmp = $ue->toArray();
                $tmp['created_at_str'] = Date('Y-m-d H::s', $ue->created_at);
                $tmp['user_ebook_res'] = $ue->ebookres;
                $tmp['mission_ct'] = !empty($ue->ebookres) ? sizeof($ue->ebookres) : 0;

                $ret[] = $tmp;
            }
        }

        return $ret;
    }

    public function getUserOneEbook() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $ebookId = !empty($this->_get['user_ebook_id']) ? $this->_get['user_ebook_id'] : 0;

//        if (empty($userId)) {
//            throw new \Exception('用户ID不能为空', ErrorCode::EBOOK_USER_ID_EMPTY);
//        }

        $userEbook = UserEBook::find()
            ->where(['user_id' => $userId])
            ->andFilterWhere(['id' => $ebookId])
//            ->orderBy(['id' => SORT_DESC])
            ->one();

        $ret = [];

        if (!empty($userEbook)) {
            $tmp = $userEbook->toArray();
            $tmp['created_at_str'] = Date('Y-m-d H::s', $userEbook->created_at);
            $tmp['user_ebook_res'] = $userEbook->ebookres;
            $ret = $tmp;
        }

        return $ret;
    }

    public function getUserLastEbook() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

//        if (empty($userId)) {
//            throw new \Exception('用户ID不能为空', ErrorCode::EBOOK_USER_ID_EMPTY);
//        }

        $userEbook = UserEBook::find()
            ->where(['user_id' => $userId])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $ret = [];

        if (!empty($userEbook)) {
//            foreach ($userEbook as $ue) {
            $tmp = $userEbook->toArray();
            $tmp['created_at_str'] = Date('Y-m-d H::s', $userEbook->created_at);
            $tmp['user_ebook_res'] = $userEbook->ebookres;
            $ret = $tmp;
//            }
        }

        return $ret;
    }

    public function upload() {
        $file = $_FILES['fileUpload'];

        if (empty($file)) {
            throw new \Exception('上传文件不能为空', ErrorCode::EBOOK_UPLOAD_FILE_EMPTY);
        }

        $request = $_REQUEST;
        $userId = !empty($request['user_id']) ? $request['user_id'] : 0;
        $ebookStoryId = !empty($request['ebook_story_id']) ? $request['ebook_story_id'] : 0;
        $poiId = !empty($request['poi_id']) ? $request['poi_id'] : 0;

        $filePath = !empty($file['tmp_name']) ? $file['tmp_name'] : '';
        if (empty($filePath)) {
            throw new \Exception('上传文件失败', ErrorCode::EBOOK_UPLOAD_FILE_EMPTY);
        }

        try {
            $ret = Yii::$app->ebook->generateVideoBase64WithEbookStory($ebookStoryId, $userId, $poiId, $filePath);
            if ($ret === false) {
                throw new \Exception('生成视频失败: AI错误', ErrorCode::EBOOK_GEN_VIDEO_FAILED);
            }
//            $videoId = '';
//            if (!empty($ret['id'])) {
//                $videoId = $ret['id'];
//            }
        } catch (\Exception $e) {
            throw new \Exception('生成视频失败: ' . $e->getMessage(), ErrorCode::EBOOK_GEN_VIDEO_FAILED);
        }

        return [
            'msg' => '视频生成中',
        ];

    }

    public function generateVideo() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $ebookStoryId = !empty($this->_get['ebook_story_id']) ? $this->_get['ebook_story_id'] : 0;
        $poiId = !empty($this->_get['poi_id']) ? $this->_get['poi_id'] : 0;
        $imageUrl = !empty($this->_get['image_url']) ? $this->_get['image_url'] : '';

        try {
            $ret = Yii::$app->ebook->generateVideoWithEbookStory($ebookStoryId, $userId, $poiId, $imageUrl);
            $videoId = '';
            if (!empty($ret['id'])) {
                $videoId = $ret['id'];
            }
        } catch (\Exception $e) {
            throw new \Exception('生成视频失败: ' . $e->getMessage(), ErrorCode::EBOOK_GEN_VIDEO_FAILED);
        }

        return [
            'msg' => '视频生成中，ID：' . $videoId,
        ];
    }



    public function getStory() {

        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

//        $story = !empty(UserEBook::$storyList[$storyId]) ? UserEBook::$storyList[$storyId] : [];
//        $story['poi'] = !empty(UserEBook::$poiList[$story['poi']])
//            ? UserEBook::$poiList[$story['poi']] : [];

        $story = !empty(UserEBook::$poiList[$storyId]) ? UserEBook::$poiList[$storyId] : [];

        return $story;

    }



}