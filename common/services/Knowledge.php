<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/2/20
 * Time: 2:12 PM
 */

namespace common\services;


use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Session;
use common\models\UserKnowledge;
use yii\base\Component;
use yii;

class Knowledge extends Component
{
    public function get($knowledgeId, $sessionId, $userId) {
        $userKnowledge = UserKnowledge::find()
            ->where([
                'user_id' => $userId,
                'knowledge_id' => $knowledgeId,
                'session_id' => $sessionId,
            ])->one();

        return $userKnowledge;
    }

    public function set($knowledgeId, $sessionId, $sessionStageId, $userId, $storyId, $act = 'complete') {

        $knowledge = \common\models\Knowledge::findOne($knowledgeId);

        if (empty($knowledge)) {
            throw new \Exception('知识点不存在', ErrorCode::USER_KNOWLEDGE_NOT_FOUND);
        }

        if (empty($sessionId)) {
            throw new \Exception('没有找到场次', ErrorCode::SESSION_NOT_FOUND);
        }

        $sessionInfo = Session::findOne($sessionId);

        if (empty($sessionInfo)
            || ($sessionInfo->session_status == Session::SESSION_STATUS_CANCEL
                or $sessionInfo->session_status == Session::SESSION_STATUS_FINISH
            )
        ) {
            throw new \Exception('场次不存在', ErrorCode::SESSION_NOT_FOUND);
        }

        $userKnowledge = UserKnowledge::find()
            ->where([
                'user_id' => $userId,
                'knowledge_id' => $knowledgeId,
                'session_id' => $sessionId,
            ])->one();

        try {
            if (!empty($userKnowledge)) {
                $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_COMPLETE;
            } else {
                $userKnowledge = new UserKnowledge();
                $userKnowledge->user_id = $userId;
                $userKnowledge->knowledge_id = $knowledgeId;
                $userKnowledge->session_id = $sessionId;
                $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_COMPLETE;
            }
            $userKnowledge->save();

            if ($act == 'complete') {
                if ($knowledge->knowledge_class == \common\models\Knowledge::KNOWLEDGE_CLASS_MISSSION) {

//                    if (!empty($sessionStageId)) {
                        Yii::$app->act->read($sessionId, $sessionStageId, $userId, [
                            Actions::ACTION_TYPE_ACTION,
                            Actions::ACTION_TYPE_MSG,
                        ]);
//                    }
                    Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, '您完成了任务：' . $knowledge->title, Actions::ACTION_TYPE_MSG);

                } else {
                    Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, '您获得了知识：' . $knowledge->title, Actions::ACTION_TYPE_MSG);
                }
            } elseif ($act == 'process') {
                if ($knowledge->knowledge_class == \common\models\Knowledge::KNOWLEDGE_CLASS_MISSSION) {
                    Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, '您开启了任务：' . $knowledge->title, Actions::ACTION_TYPE_MSG);
                } else {
                    Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, '您获得了知识：' . $knowledge->title, Actions::ACTION_TYPE_MSG);
                }
            }

        } catch (\Exception $e) {
            throw new \Exception('完成进程失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
        }

        if ($act == 'complete') {
            // 更新新知识点
            $nextKnowledges = \common\models\Knowledge::find()
                ->where(['story_id' => $storyId])
                ->andWhere(['pre_knowledge_id' => $knowledge->id])
                ->all();

            if (!empty($nextKnowledges)) {
                try {
                    foreach ($nextKnowledges as $nextKnowledge) {
                        $nextUserKnowledge = UserKnowledge::find()
                            ->where([
                                'user_id' => $userId,
                                'knowledge_id' => $nextKnowledge->id,
                                'session_id' => $sessionId,
                            ])->one();
                        if (empty($nextUserKnowledge)) {
                            $nextUserKnowledge = new UserKnowledge();
                            $nextUserKnowledge->user_id = $userId;
                            $nextUserKnowledge->knowledge_id = $nextKnowledge->id;
                            $nextUserKnowledge->session_id = $sessionId;
                        }
                        if ($nextKnowledge->knowledge_class == \common\models\Knowledge::KNOWLEDGE_CLASS_MISSSION) {
                            $nextUserKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
                        } else {
                            $nextUserKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_COMPLETE;
                        }
                        $nextUserKnowledge->save();

                        if ($nextKnowledge->knowledge_class == \common\models\Knowledge::KNOWLEDGE_CLASS_MISSSION
                            && empty($nextMission)
                        ) {
                            $nextMission = $nextKnowledge;
                        } else {
                            Yii::$app->act->add($sessionId, $sessionStageId, $userId, '您获得了知识：' . $nextKnowledge->title, Actions::ACTION_TYPE_MSG);
                        }
                    }

                    if (!empty($nextMission)) {
                        Yii::$app->act->add($sessionId, $sessionStageId, $userId, '下一个任务：' . $nextMission->title, Actions::ACTION_TYPE_MSG);
                    }
                } catch (\Exception $e) {
                    throw new \Exception('更新下一个知识点失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
                }
            } else {
//            Yii::$app->act->add($sessionId, $userId, '任务全部完成啦！', Actions::ACTION_TYPE_ACTION);
            }
        }

        return $userKnowledge;
    }

    public function setByItem($itemId, $itemType, $sessionId, $sessionStageId, $userId, $storyId) {
        $itemKnowledgeList = ItemKnowledge::find()
            ->where([
                'item_id' => $itemId,
                'item_type' => $itemType,
                'story_id' => $storyId,
            ])
            ->asArray()
            ->all();

        if (!empty($itemKnowledgeList)) {
            foreach ($itemKnowledgeList as $itemKnowledge) {
                $this->set($itemKnowledge['knowledge_id'], $sessionId, $sessionStageId, $userId, $storyId, $itemKnowledge['knowledge_set_status']);
            }
        }
    }

}