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

    public function getAllByDesc($sessionId, $userId, $knowledgeClass = 0, $offset = 0, $limit = 20) {
        $userKnowledge = UserKnowledge::find()
            ->joinWith('knowledge')
            ->where([
                'user_id' => $userId,
                'session_id' => $sessionId,
            ]);
        if (!empty($knowledgeClass)) {
            $userKnowledge = $userKnowledge->andFilterWhere([
                'o_knowledge.knowledge_class' => $knowledgeClass
            ]);
        }
        $userKnowledge= $userKnowledge->orderBy([
                'id'    => SORT_DESC
            ])
            ->offset($offset)
            ->limit($limit)
            ->all();

        return $userKnowledge;
    }

    public function isRead($knowledgeId, $sessionId, $userId, $storyId) {
        $userKnowledge = UserKnowledge::find()
            ->where([
                'user_id' => $userId,
                'knowledge_id' => $knowledgeId,
                'session_id' => $sessionId,
            ])->one();

        if (!empty($userKnowledge)) {
            $userKnowledge->is_read = UserKnowledge::KNOWLEDGE_IS_READ_YES;
            $userKnowledge->save();
        } else {
            throw new \Exception('用户没有接受知识/任务', ErrorCode::USER_KNOWLEDGE_NOT_FOUND);
        }
    }

    public function isReadByUserKnowledgeId($userKnowledgeId) {
        $userKnowledge = UserKnowledge::find()
            ->where([
                'id' => $userKnowledgeId,
            ])->one();

        if (!empty($userKnowledge)) {
            $userKnowledge->is_read = UserKnowledge::KNOWLEDGE_IS_READ_YES;
            $userKnowledge->save();
        } else {
            throw new \Exception('用户没有接受知识/任务', ErrorCode::USER_KNOWLEDGE_NOT_FOUND);
        }
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
                switch ($act) {
                    case 'process':
                        $userKnowledgeStatus = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
                        break;
                    case 'complete':
                    default:
                        $userKnowledgeStatus = UserKnowledge::KNOWLDEGE_STATUS_COMPLETE;
                        break;
                }
                if (!empty($userKnowledge)) {
                    $userKnowledge->knowledge_status = $userKnowledgeStatus;
                } else {
                    $userKnowledge = new UserKnowledge();
                    $userKnowledge->user_id = $userId;
                    $userKnowledge->knowledge_id = $knowledgeId;
                    $userKnowledge->session_id = $sessionId;
                    $userKnowledge->knowledge_status = $userKnowledgeStatus;
                }
                $userKnowledge->save();

                if ($act == 'complete') {
                    if ($knowledge->knowledge_class == \common\models\Knowledge::KNOWLEDGE_CLASS_MISSSION) {

//                    if (!empty($sessionStageId)) {
                        // $sessionStageId
                        // $sessionStageId 变量先提出来，用0全覆盖试试，要不总是有很多漏网数据不能被read，就还提示出来
                        Yii::$app->act->read($sessionId, 0, $userId, [
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
                            Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, '您获得了知识：' . $nextKnowledge->title, Actions::ACTION_TYPE_MSG);
                        }
                    }

                    if (!empty($nextMission)) {
                        Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, '开启任务：' . $nextMission->title, Actions::ACTION_TYPE_MSG);
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

    public function removeByStage($storyStageId, $sessionId, $userId, $storyId) {
        if (empty($storyStageId)) {
            return false;
        }
        $knowledgeList = \common\models\Knowledge::find()
            ->where([
                'story_stage_id' => $storyStageId,
                'knowledge_class' => \common\models\Knowledge::KNOWLEDGE_CLASS_MISSSION,
                'story_id'  => $storyId,
            ])
            ->asArray()
            ->all();

        if (!empty($knowledgeList)) {
            foreach ($knowledgeList as $knowledge) {
                $this->remove($knowledge['id'], $sessionId, $userId, $storyId, true);
            }
        }
    }

    public function remove($knowledgeId, $sessionId, $userId, $storyId, $isForce = false) {
        $knowledge = \common\models\Knowledge::findOne($knowledgeId);
        if (empty($knowledge)
            && !$isForce
        ) {
            throw new \Exception('知识点不存在', ErrorCode::USER_KNOWLEDGE_NOT_FOUND);
        } elseif (empty($knowledge) && $isForce) {
            return false;
        }

        $userKnowledge = UserKnowledge::find()
            ->where([
                'user_id' => $userId,
                'knowledge_id' => $knowledgeId,
                'session_id' => $sessionId,
            ])->one();

        if (empty($userKnowledge)
            && !$isForce
        ) {
            throw new \Exception('知识点不存在', ErrorCode::USER_KNOWLEDGE_NOT_FOUND);
        } elseif (
            empty($userKnowledge)
            && $isForce
        ) {
            return false;
        }

        try {
//            $userKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_REMOVE;
//            $userKnowledge->save();
            $userKnowledge->delete();

        } catch (\Exception $e) {
            throw new \Exception('删除进程失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
        }

        return true;
    }

}