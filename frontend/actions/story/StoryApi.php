<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\story;


use common\definitions\ErrorCode;
use common\models\Session;
use common\models\Story;
use common\models\StoryExtend;
use common\models\StoryGoal;
use common\models\StoryRole;
use frontend\actions\ApiAction;
use Yii;

class StoryApi extends ApiAction
{
    public $action;
    private $_get;

    public function run()
    {
        $this->_get = Yii::$app->request->get();


        switch ($this->action) {
            case 'all':
                $ret = $this->getStoryList();
                break;
            case 'detail':
                $ret = $this->getStoryDetail();
                break;
            case 'goal':
                $ret = $this->getStoryGoal();
                break;
            case 'role':
                $ret = $this->getStoryRole();
                break;
            case 'session':
                $ret = $this->getStorySession();
                break;
            default:
                $ret = [];
                break;

        }

        return $this->success($ret);
    }

    // 获取故事详情
    public function getStoryDetail() {

        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        if (empty($storyId)) {
            return $this->fail('故事ID不能为空', ErrorCode::STORY_NOT_FOUND);
        }

        $story = Story::find()
            ->where(['id' => $storyId])
            ->one();

        $story['extend'] = StoryExtend::findOne(['story_id' => $storyId]);

        return $story;
    }

    // 获取故事结果
    public function getStoryGoal() {
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        if (empty($storyId)) {
            return $this->fail('故事ID不能为空', ErrorCode::STORY_NOT_FOUND);
        }

        $story = StoryGoal::find()
            ->where(['story_id' => $storyId])
            ->all();

        if (!empty($story['selected'])) {
            $story['selected'] = json_decode($story['selected'], true);
        }

        return $story;
    }

    // 获得故事角色
    public function getStoryRole() {
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        if (empty($storyId)) {
            return $this->fail('故事ID不能为空', ErrorCode::STORY_NOT_FOUND);
        }

        $story = StoryRole::find()
            ->where(['story_id' => $storyId])
            ->all();

        return $story;
    }

    // 获得故事场景
    public function getStorySession() {
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        if (empty($storyId)) {
            return $this->fail('故事ID不能为空', ErrorCode::STORY_NOT_FOUND);
        }

        $session = Session::find()
            ->where(['story_id' => $storyId])
            ->all();

        return $session;
    }

    public function getStoryList() {

        $limit = !empty($this->_get['limit']) ? $this->_get['limit'] : 20;
        $offset = !empty($this->_get['offset']) ? $this->_get['offset'] : 0;

        $tagId = '';
        if (!empty($this->_get['tag_id'])) {
            $tagId = $this->_get['tag_id'];
        }

        if (!empty($tagId) && strpos($tagId, ',')) {
            $tagIds = explode(',', $tagId);
        } else {
            $tagIds = $tagId;
        }

        $storyList = Story::find();
        if (!empty($tagIds)) {
            $storyList = $storyList
                ->joinWith('storyTag')
                ->where(['story_tag.tag_id' => $tagIds]);
        }
        $storyList = $storyList->offset($offset)
            ->limit($limit)
            ->all();

        return $storyList;
    }



}