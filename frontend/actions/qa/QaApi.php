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
use common\models\ItemKnowledge;
use common\models\Qa;
use common\models\SessionQa;
use common\models\StoryMatch;
use common\models\StoryStages;
use common\models\UserQa;
use common\models\User;
//use liyifei\base\actions\ApiAction;
use common\models\UserList;
use common\models\UserScore;
use common\models\UserStory;
use frontend\actions\ApiAction;
use yii;

class QaApi extends ApiAction
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
            if (empty($this->_storyId)) {
                throw new \Exception('剧本不存在', ErrorCode::STORY_NOT_FOUND);
            }

            switch ($this->action) {
                case 'get_qa_list':
                    $ret = $this->getQaList();
                    break;
                case 'get_session_qa_list':
                    $ret = $this->getSessionQaList();
                    break;
                case 'get_user_qa_list':
                    $ret = $this->getUserQaList();
                    break;
                case 'add_user_answer':
                    $ret = $this->addUserAnswer();
                    break;
                case 'get_qa':
                    $ret = $this->getQa();
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
    public function getQaList() {
        $qaType = !empty($this->_get['qa_type']) ? $this->_get['qa_type'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $offset = !empty($this->_get['offset']) ? $this->_get['offset'] : 0;
        $limit = !empty($this->_get['limit']) ? $this->_get['limit'] : 20;

        $qa = Qa::find()
            ->where(['story_id' => $storyId]);
        if (!empty($qaType)) {
            $qa = $qa->where(['qa_type' => $qaType]);
        }
        $qa = $qa->offset($offset)->limit($limit)->orderBy('id desc')->asArray()->all();

        return $qa;

    }

    public function getSessionQaList() {
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $qaType = !empty($this->_get['qa_type']) ? $this->_get['qa_type'] : 0;
        $offset = !empty($this->_get['offset']) ? $this->_get['offset'] : 0;
        $limit = !empty($this->_get['limit']) ? $this->_get['limit'] : 20;

        $qa = SessionQa::find()
            ->where(['story_id' => $storyId]);
        if (!empty($sessionId)) {
            $qa = $qa->where(['session_id' => $sessionId]);
        }
        if (!empty($qaType)) {
            $qa = $qa->where(['qa_type' => $qaType]);
        }
        $qa = $qa->offset($offset)->limit($limit)->orderBy('id desc')->asArray()->all();

        return $qa;

    }

    public function getUserQaList() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $qaType = !empty($this->_get['qa_type']) ? $this->_get['qa_type'] : 0;
        $offset = !empty($this->_get['offset']) ? $this->_get['offset'] : 0;
        $limit = !empty($this->_get['limit']) ? $this->_get['limit'] : 20;

        $qa = UserQa::find()
            ->where(['story_id' => $storyId]);
        if (!empty($userId)) {
            $qa = $qa->where(['user_id' => $userId]);
        }
        if (!empty($qaType)) {
            $qa = $qa->where(['qa_type' => $qaType]);
        }
        $qa = $qa->offset($offset)->limit($limit)->orderBy('id desc')->asArray()->all();

        return $qa;
    }

    public function getQa() {
        $qaId = !empty($this->_get['qa_id']) ? $this->_get['qa_id'] : 0;
        $qa = Qa::find()->where(['id' => $qaId])->asArray()->one();

        if (!empty($qa['st_selected'])
            && \common\helpers\Common::isJson($qa['st_selected'])
        ) {
            $qa['st_selected'] = json_decode($qa['st_selected'], true);
        }

        return $qa;
    }

    public function addUserAnswer() {
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $answer = !empty($this->_get['answer']) ? $this->_get['answer'] : '';
        $qaId = !empty($this->_get['qa_id']) ? $this->_get['qa_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $beginTs = !empty($this->_get['begin_ts']) ? $this->_get['begin_ts'] : 0;

        $stAnswer = !empty($this->_get['st_answer']) ? $this->_get['st_answer'] : '';

        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        $level = !empty($this->_get['level']) ? $this->_get['level'] : 0;
        $linkQaId = !empty($this->_get['link_qa_id']) ? $this->_get['link_qa_id'] : 0;

        $qaMode = !empty($this->_get['qa_mode']) ? $this->_get['qa_mode'] : Qa::QA_MODE_NORMAL;

        $qa = Qa::find()->where(['id' => $qaId])->asArray()->one();

        if ($qaMode == Qa::QA_MODE_NORMAL && empty($qa)) {
            throw new \Exception('问答不存在', ErrorCode::QA_NOT_EXIST);
        } else if (empty($qa)) {
            $topic = !empty($this->_get['topic']) ? $this->_get['topic'] : '';
            if (empty($topic)) {
                throw new \Exception('您提供的问答参数不完整', ErrorCode::QA_PARAMETERS_INVALID);
            }

            $qa = Qa::find()
                    ->where([
                        'topic' => $topic,
                        'qa_mode' => $qaMode,
                    ])
                    ->one();
            if (empty($qa)) {

                $selected = !empty($this->_get['selected']) ? $this->_get['selected'] : '';
                // 如果没有指定TYPE，默认按照GPT TYPE
                $qaType = !empty($this->_get['qa_type']) ? $this->_get['qa_type'] : Qa::QA_TYPE_GPT_SUBJECT;
//                $qaClass = !empty($this->_get['qa_class']) ? $this->_get['qa_class'] : Qa::QA_CLASS_MATH;
                $matchClass = !empty($this->_get['match_class']) ? $this->_get['match_class'] : StoryMatch::MATCH_CLASS_MATH;
                // 默认10金币
                $score = !empty($this->_get['score']) ? $this->_get['score'] : 10;
                $stSelected = !empty($this->_get['st_selected']) ? $this->_get['st_selected'] : '';
                $prop = !empty($this->_get['prop']) ? $this->_get['prop'] : '';

                $qaClass = !empty(StoryMatch::$matchClass2QaClass[$matchClass]) ? StoryMatch::$matchClass2QaClass[$matchClass] : Qa::QA_CLASS_MATH;

                $qa = new Qa();
                $qa->topic = $topic;
                $qa->qa_type = $qaType;
                $qa->qa_class = $qaClass;
                $qa->qa_mode = $qaMode;
                $qa->story_id = $storyId;
                $qa->selected = $selected;
                $qa->st_answer = $stAnswer;
                $qa->st_selected = $stSelected;
                $qa->score = $score;

                $qa->link_qa_id = $linkQaId;
                $qa->level = $level;

                $qa->prop = $prop;
                $qa->save();
                $qaId = $qa->id = Yii::$app->db->getLastInsertID();
            } else {
                $qaId = $qa->id;
            }


        }

        $isRight = UserQa::ANSWER_WRONG;
        if (!empty($stAnswer) && $stAnswer == $answer) {
            $isRight = UserQa::ANSWER_RIGHT;
        } elseif (!empty($stAnswer) && $stAnswer != $answer) {
            $isRight = UserQa::ANSWER_WRONG;
        } elseif (empty($stAnswer)) {
            if ($qa['st_selected'] == $answer) {
                $isRight = UserQa::ANSWER_RIGHT;
            } else {
                $isRight = UserQa::ANSWER_WRONG;
            }
        }


        $userQa = UserQa::find()->where([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'qa_id' => $qaId,
        ]);
        if ($qaMode != Qa::QA_MODE_NORMAL) {
            $userQa = $userQa->andFilterWhere([
                'is_right' => $isRight,
            ]);
        }
        $userQa = $userQa->andFilterWhere([
                '>', 'created_at', time() - 86400,
            ])
            ->one();

        if ($qa['qa_type'] == Qa::QA_TYPE_CHATGPT) {
            $knowledges = [];
            if (!empty($qa['knowledge_id'])) {
                $knowledgesData = Yii::$app->knowledge->get($qa['knowledge_id'], $sessionId, $userId);
                if (!empty($knowledgesData->knowledge)) {
                    $knowledges = $knowledgesData->knowledge;
                }
            }

            $response = Yii::$app->chatgpt->callOpenAIChatGPT($answer, $knowledges);
            if (!empty($response['choices'][0]['message']['content'])) {
                $ret['msg'] = $response['choices'][0]['message']['content'];
                $ret['voice'] = Yii::$app->chatgpt->text2Speech($ret['msg']);

            } else {
                var_dump($response);
                $ret['msg'] = '可能遇到一些错误，请您稍后再试……';
            }
            return $ret;
        }

        try {
            $transaction = Yii::$app->db->beginTransaction();

            if (!empty($sessionId)) {
                $sessionQa = SessionQa::find()->where(['session_id' => $sessionId, 'qa_id' => $qaId])->one();
                if (!empty($sessionQa)) {
                    $sessionQa->is_answer = SessionQa::SESSION_QA_STATUS_IS_ANSWER;
                    $sessionQa->is_right = $isRight;
                    $saveRet = $sessionQa->save();
                }
            }

            $scoreRets = [
                'score' => 0,
                'x' => 1.00,
                'addtion' => 0,
            ];

            if ($isRight == 1) {
                // 临时处理
                // 10w以上目前认为是小程序可能调用的场次
                // 未来需要扩展，增加third字段
                if (!empty($sessionId) && $sessionId < 100000) {
                    if (!empty($qa['knowledge_id'])) {
                        Yii::$app->knowledge->set($qa['knowledge_id'], $sessionId, $sessionStageId, $userId, $qa['story_id'], 'complete');
                    }

                    Yii::$app->knowledge->setByItem($qa['id'], ItemKnowledge::ITEM_TYPE_QA, $sessionId, $sessionStageId, $userId, $qa['story_id']);


                    if (!empty($qa['score'])) {
                        $score = $qa['score'];

                        $scoreRets = Yii::$app->score->computeWithQa($userId, $storyId, $sessionId, $qaId, $score, $beginTs);

                        if (!empty($scoreRets)) {
                            $score = $scoreRets['score'];
//                        $x = $scoreRets['x'];
//                        $addition = $scoreRets['addition'];
                        }

                        Yii::$app->score->add($userId, $storyId, $sessionId, $sessionStageId, $score);
                    }
                    //            $isNew = true;


//                $itemKnowledgeList = ItemKnowledge::find()
//                    ->where([
//                        'item_id' => $qa['id'],
//                        'item_type' => ItemKnowledge::ITEM_TYPE_QA,
//                        'story_id' => $qa['story_id'],
//                    ])
//                    ->asArray()
//                    ->all();
//
//                if (!empty($itemKnowledgeList)) {
//                    foreach ($itemKnowledgeList as $itemKnowledge) {
//                        Yii::$app->knowledge->complete($itemKnowledge['knowledge_id'], $sessionId, $userId, $qa['story_id']);
//                    }
//                }

                    if (!empty($qa['story_stage_id'])) {
                        $storyStage = StoryStages::findOne($qa['story_stage_id']);
                        $expirationInterval = 60;        // 消息超时时间
                        Yii::$app->act->add($sessionId, $sessionStageId, $storyId, $userId, $storyStage['stage_u_id'], Actions::ACTION_TYPE_CHANGE_STAGE, $expirationInterval);
                    }
                }
            }
            if (empty($userQa)) {
                $userQa = new UserQa();
                $userQa->story_id = $qa['story_id'];
                $userQa->session_id = $sessionId;
                $userQa->user_id = $userId;
                $userQa->qa_id = $qaId;
                $userQa->answer = $answer;
                $userQa->is_right = $isRight;
                $saveRet = $userQa->save();
                $userQaId = Yii::$app->db->getLastInsertID();
                $userQa->id = $userQaId;

            } else {
                $userQa->answer = $answer;
                $userQa->is_right = $isRight;
                $saveRet = $userQa->save();
            }

            $propJson = json_decode($qa['prop'], true);

            if (!empty($propJson['model_inst_u_ids'])) {
//                $expirationInterval = 600;
                foreach ($propJson['model_inst_u_ids'] as $modelUId => $modelUParams) {
                    Yii::$app->act->hideModels((int)$sessionId, $storyId, $modelUId);
                }
            }

            $transaction->commit();

        } catch (\Exception $e) {
            var_dump($e);
            $transaction->rollBack();
            throw new \Exception('添加用户问答失败', ErrorCode::QA_SAVE_FAILED);
        }

        $ret['user_qa'] = $userQa;
        $ret['score'] = $scoreRets;

        return $ret;
    }

}