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
                case 'get_story':
                    $ret = $this->getStory();
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
        $storyList = UserEBook::$storyList;

        if (empty($storyList)) {
            return [];
        }

        return $storyList;
    }

    public function upload() {
        $file = $_FILES['fileUpload'];

        if (empty($file)) {
            throw new \Exception('上传文件不能为空', ErrorCode::EBOOK_UPLOAD_FILE_EMPTY);
        }

        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $ebookStoryId = !empty($this->_get['ebook_story_id']) ? $this->_get['ebook_story_id'] : 0;
        $poiId = !empty($this->_get['poi_id']) ? $this->_get['poi_id'] : 0;

        try {
            $ret = Yii::$app->ebook->generateVideoBase64WithEbookStory($ebookStoryId, $userId, $poiId, $file);
            $videoId = '';
            if (!empty($ret['id'])) {
                $videoId = $ret['id'];
            }
        } catch (\Exception $e) {
            throw \Exception('生成视频失败: ' . $e->getMessage(), ErrorCode::EBOOK_GEN_VIDEO_FAILED);
        }

        return [
            'msg' => '视频生成中，ID：' . $videoId,
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
            throw \Exception('生成视频失败: ' . $e->getMessage(), ErrorCode::EBOOK_GEN_VIDEO_FAILED);
        }

        return [
            'msg' => '视频生成中，ID：' . $videoId,
        ];
    }



    public function getStory() {

        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        $story = !empty(UserEBook::$storyList[$storyId]) ? UserEBook::$storyList[$storyId] : [];

        return $story;

    }



}