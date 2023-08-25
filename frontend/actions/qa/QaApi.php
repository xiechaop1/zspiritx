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
use common\models\Qa;
use common\models\SessionQa;
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

    private $_get;

    public function run()
    {
        try {
            $this->_get = Yii::$app->request->get();
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
            && is_json($qa['st_selected'])
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

        $qa = Qa::find()->where(['id' => $qaId])->asArray()->one();

        if (empty($qa)) {
            throw new \Exception('问答不存在', ErrorCode::QA_NOT_EXIST);
        }

        $userQa = UserQa::find()->where(['user_id' => $userId, 'qa_id' => $qaId])->asArray()->one();

        try {
            $transaction = Yii::$app->db->beginTransaction();
            if ($qa['st_selected'] == $answer) {
                $isRight = 1;
            } else {
                $isRight = 0;
            }
            if (empty($userQa)) {
                $userQa = new UserQa();
                $userQa->user_id = $userId;
                $userQa->qa_id = $qaId;
                $userQa->answer = $answer;
                $userQa->is_right = $isRight;
            } else {
                $userQa->answer = $answer;
                $userQa->is_right = $isRight;
            }
            $ret = $userQa->save();

            if (!empty($sessionId)) {
                $sessionQa = SessionQa::find()->where(['session_id' => $sessionId, 'qa_id' => $qaId])->asArray()->one();
                $sessionQa->is_answer = SessionQa::SESSION_QA_STATUS_IS_ANSWER;
                $sessionQa->is_right = $isRight;
                $ret = $sessionQa->save();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception('添加用户问答失败', ErrorCode::QA_SAVE_FAILED);
        }

        return $ret;
    }

}