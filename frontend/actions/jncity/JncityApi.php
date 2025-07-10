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
        $poiList = UserBook::$poiList;

        return $poiList;

    }

    public function storyList() {
        $storyList = UserBook::$storyList;

        if (empty($storyList)) {
            return [];
        }

        return $storyList;
    }

    public function getStory() {

        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;

        $story = !empty(UserBook::$storyList[$storyId]) ? UserBook::$storyList[$storyId] : [];

        return $story;

    }



}