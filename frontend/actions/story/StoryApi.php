<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\story;


use common\models\Story;
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
            default:
                $ret = [];
                break;

        }

        return $this->success($ret);
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
            $storyList = $storyList->
                joinWith('storyTag')
                ->where(['story_tag.tag_id' => $tagIds]);
        }
        $storyList = $storyList->offset($offset)
            ->limit($limit)
            ->all();

        return $storyList;
    }



}