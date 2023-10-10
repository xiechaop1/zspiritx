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
use common\models\Session;
use common\models\UserKnowledge;
use yii\base\Component;
use yii;

class Knowledge extends Component
{

    public function complete($knowledgeId, $sessionId, $userId, $storyId) {

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
        } catch (\Exception $e) {
            throw new \Exception('完成进程失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
        }

        // 更新新知识点
        $nextKnowledges = \common\models\Knowledge::find()
            ->where(['story_id' => $storyId])
            ->andWhere(['pre_knowledge_id' => $knowledge->id])
            ->all();

        if (!empty($nextKnowledge)) {
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
                    $nextUserKnowledge->knowledge_status = UserKnowledge::KNOWLDEGE_STATUS_PROCESS;
                    $nextUserKnowledge->save();
                }

                Yii::$app->act->add($sessionId, $userId, '可以去寻找下一个任务：' . $nextKnowledge->title, Actions::ACTION_TYPE_MSG);
            } catch (\Exception $e) {
                throw new \Exception('更新下一个知识点失败', ErrorCode::USER_KNOWLEDGE_OPERATE_FAILED);
            }
        } else {
//            Yii::$app->act->add($sessionId, $userId, '任务全部完成啦！', Actions::ACTION_TYPE_ACTION);
        }
    }

}