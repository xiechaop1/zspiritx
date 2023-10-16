<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\qa;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\Knowledge;
use common\models\Qa;
use common\models\Session;
use common\models\SessionQa;
use common\models\UserKnowledge;
use common\models\UserQa;
use common\models\User;
//use liyifei\base\actions\ApiAction;
use common\models\UserList;
use common\models\UserScore;
use common\models\UserStory;
use frontend\actions\ApiAction;
use yii;

class KnowledgeApi extends ApiAction
{
    public $action;

//    public $userId;

    private $_storyId;
    private $_sessionId;

    private $_userId;

    private $_get;

    public function run()
    {
        $this->_get = Yii::$app->request->get();


        try {
            $this->_storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
            $this->_sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
            $this->_userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;

            switch ($this->action) {
                case 'complete_knowledge':
                    $ret = $this->completeKnowledge();
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


    public function completeKnowledge() {
        $knowledgeId = !empty($this->_get['knowledge_id']) ? $this->_get['knowledge_id'] : 0;

        $knowledge = Knowledge::findOne($knowledgeId);

        if (empty($knowledge)) {
            throw new \Exception('知识点不存在', ErrorCode::USER_KNOWLEDGE_NOT_FOUND);
        }

        if (empty($this->_sessionId)) {
            throw new \Exception('没有找到场次', ErrorCode::SESSION_NOT_FOUND);
        }

        $sessionInfo = Session::findOne($this->_sessionId);

        if (empty($sessionInfo)
            || ($sessionInfo->session_status = Session::SESSION_STATUS_CANCEL
                or $sessionInfo->session_status = Session::SESSION_STATUS_FINISH
            )
        ) {
            throw new \Exception('场次不存在', ErrorCode::SESSION_NOT_FOUND);
        }

        $userKnowledge = UserKnowledge::find()
            ->where([
                'user_id' => $this->_userId,
                'knowledge_id' => $knowledgeId,
                'session_id' => $this->_sessionId,
            ])->one();

        try {
            if (!empty($userKnowledge)) {
                $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_COMPLETE;
            } else {
                $userKnowledge = new UserKnowledge();
                $userKnowledge->user_id = $this->_userId;
                $userKnowledge->knowledge_id = $knowledgeId;
                $userKnowledge->session_id = $this->_sessionId;
                $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_COMPLETE;
            }
            $userKnowledge->save();
        } catch (\Exception $e) {
            throw new \Exception('完成进程失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
        }

        // 更新新知识点
        $nextKnowledges = Knowledge::find()
            ->where(['story_id' => $this->_storyId])
            ->andWhere(['pre_knowledge_id' => $knowledge->id])
            ->all();

        if (!empty($nextKnowledge)) {
            try {
                foreach ($nextKnowledges as $nextKnowledge) {
                    $nextUserKnowledge = UserKnowledge::find()
                        ->where([
                            'user_id' => $this->_userId,
                            'knowledge_id' => $nextKnowledge->id,
                            'session_id' => $this->_sessionId,
                        ])->one();
                    if (empty($nextUserKnowledge)) {
                        $nextUserKnowledge = new UserKnowledge();
                        $nextUserKnowledge->user_id = $this->_userId;
                        $nextUserKnowledge->knowledge_id = $nextKnowledge->id;
                        $nextUserKnowledge->session_id = $this->_sessionId;
                    }
                    $nextUserKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
                    $nextUserKnowledge->save();

                    if ($nextKnowledge->knowledge_class == Knowledge::KNOWLEDGE_CLASS_MISSSION) {
                        $lastKnowledge = $nextKnowledge;
                    }
                }

                Yii::$app->act->add($this->_sessionId, $this->_userId, '可以去寻找下一个任务：' . $lastKnowledge->title, Actions::ACTION_TYPE_MSG);
            } catch (\Exception $e) {
                throw new \Exception('更新下一个知识点失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
            }
        } else {
            Yii::$app->act->add($this->_sessionId, $this->_userId, '任务全部完成啦！', Actions::ACTION_TYPE_ACTION);
        }
    }

}