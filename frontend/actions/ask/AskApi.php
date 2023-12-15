<?php
/**
 * Created by PhpStorm.
 * User: xiechao
 * Date: 2019/11/01
 * Time: 4:57 PM
 */

namespace frontend\actions\ask;


use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\ItemKnowledge;
use common\models\Qa;
use common\models\SessionQa;
use common\models\StoryStages;
use common\models\UserQa;
use frontend\actions\ApiAction;
use yii;

//use liyifei\base\actions\ApiAction;

class AskApi extends ApiAction
{
    public $action;

//    public $userId;

    private $_storyId;

    private $_story;

    private $_request;
    
    
    public function run()
    {

        $this->_request = $_REQUEST;

        try {
            $this->_storyId = !empty($this->_request['story_id']) ? $this->_request['story_id'] : 0;

            switch ($this->action) {
                case 'say':
                    $ret = $this->Say();
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

    public function Say() {
        $userId = !empty($this->_request['user_id']) ? $this->_request['user_id'] : 0;
        $sessionId = !empty($this->_request['session_id']) ? $this->_request['session_id'] : 0;
        $answer = !empty($this->_request['answer']) ? $this->_request['answer'] : '';
        $knowledgeId = !empty($this->_request['knowledge_id']) ? $this->_request['knowledge_id'] : 0;
        $oldAnswer = !empty($this->_request['old_answer']) ? html_entity_decode($this->_request['old_answer']) : '';
//        $qaId = !empty($this->_request['qa_id']) ? $this->_request['qa_id'] : 0;
        $storyId = !empty($this->_request['story_id']) ? $this->_request['story_id'] : 0;

        $sessionStageId = !empty($this->_request['session_stage_id']) ? $this->_request['session_stage_id'] : 0;

//        $qa = Qa::find()->where(['id' => $qaId])->asArray()->one();
//
//        if (empty($qa)) {
//            throw new \Exception('问答不存在', ErrorCode::QA_NOT_EXIST);
//        }
//
//        $userQa = UserQa::findOne(['user_id' => $userId, 'session_id' => $sessionId, 'qa_id' => $qaId]);

//        if ($qa['qa_type'] == Qa::QA_TYPE_CHATGPT) {
            $knowledges = [];
            if (!empty($knowledgeId)) {
                $knowledgesData = Yii::$app->knowledge->get($knowledgeId, $sessionId, $userId);
                if (!empty($knowledgesData->knowledge)) {
                    $knowledges = $knowledgesData->knowledge;
                }
            }

            $oldAnswerArray = [];
            if (!empty($oldAnswer)) {
                $oldAnswerArray = json_decode($oldAnswer, true);
            }
//            var_dump($oldAnswerArray);exit;

            $response = Yii::$app->chatgpt->chatWithChatGPT($answer, $oldAnswerArray, $knowledges);
            if (!empty($response['choices'][0]['message']['content'])) {
                $ret['msg'] = $response['choices'][0]['message']['content'];
                $ret['voice'] = Yii::$app->chatgpt->text2Speech($ret['msg']);

                $oldAnswerArray[] = [
                    'role' => 'user',
                    'content' => $answer,
                ];
                $oldAnswerArray[] = [
                    'role' => 'assistant',
                    'content' => $ret['msg'],
                ];

                $ret['old_answer_json'] = json_encode($oldAnswerArray);
                $ret['old_answer'] = $oldAnswerArray;
            } else {
//                var_dump($response);
                if (!empty($ret['error']['message'])) {
                    $ret['msg'] = $ret['error']['message'];
                } else {
                    $ret['msg'] = '可能遇到一些错误，请您稍后再试……';
                }
            }
            return $ret;
//        }


    }

}