<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\processh5;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\models\Actions;
use common\models\Session;
use common\models\SessionModels;
use common\models\Story;
use common\models\StoryGoal;
use common\models\StoryModels;
use common\models\UserModels;
use common\models\UserStory;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Finish extends Action
{

    private $_get;

    private $_params;
    
    public function run()
    {
        $this->_get = Yii::$app->request->get();

        $sessionId = !empty($this->_get['session_id']) ? $this->_get['session_id'] : 0;
        $userId = !empty($this->_get['user_id']) ? $this->_get['user_id'] : 0;
        $storyId = !empty($this->_get['story_id']) ? $this->_get['story_id'] : 0;
        $modelId = !empty($this->_get['model_id']) ? $this->_get['model_id'] : 0;
        $storyModelId = !empty($this->_get['story_model_id']) ? $this->_get['story_model_id'] : 0;

        $sessionStageId = !empty($this->_get['session_stage_id']) ? $this->_get['session_stage_id'] : 0;

        $goal = !empty($this->_get['goal']) ? $this->_get['goal'] : '';

        $this->_params['user_id'] = $userId;
        $this->_params['session_id'] = $sessionId;
        $this->_params['story_id'] = $storyId;

        $transaction = Yii::$app->db->beginTransaction();

        $sessionInfo = Session::find()
            ->where([
                'id' => (int)$sessionId,
//                'user_id' => (int)$userId,
            ])
            ->one();

        if (empty($sessionInfo)) {
            return $this->fail('场次不存在', ErrorCode::SESSION_NOT_FOUND);
        }

        $this->_params['session_model'] = $sessionInfo;

        $storyModel = Story::find()
            ->where([
                'id' => $storyId
            ])
            ->one();

        $this->_params['story_model'] = $storyModel;

        try {
            $sessionInfo->session_status = Session::SESSION_STATUS_FINISH;
            $ret = $sessionInfo->save();

            $storyGoals = StoryGoal::find()
                ->where(['story_id' => (int)$storyId])
                ->one();

            $userStory = UserStory::findOne([
                'user_id'       =>  (int)$userId,
                'story_id'      =>  (int)$storyId,
                'session_id'    =>  (int)$sessionId,
            ]);

            if (empty($userStory)) {
                $userStory = new UserStory();
                $userStory->user_id = $userId;
                $userStory->story_id = $storyId;
                $userStory->session_id = $sessionId;
            }
            $userStory->goal = $goal;

            if (!empty($storyGoals)) {
                if ($goal == $storyGoals->goal) {
                    $userStory->goal_correct = '结论正确';
                } else {
                    $userStory->goal_correct = '结论错误，正确结论：' . $storyGoals->goal;
                }
            }
            $ret = $userStory->save();

            $this->_params['user_story'] = $userStory;

            Yii::$app->act->add($sessionId,$sessionStageId, 0, '游戏结束', Actions::ACTION_TYPE_ACTION);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->fail($e->getMessage(), $e->getCode());
        }

        $msg = '感谢您参与这场 ' . $storyModel->title . ' 游戏，期待您下次再来，我们还有很多精彩的游戏等着您！';

        return $this->succ($this->_params, $msg);

    }

    public function succ($params, $msg = '') {
        return $this->finishRender(0, $msg, $params);
    }
    public function fail($msg, $code) {
        return $this->finishRender($code, $msg, $this->_params);
    }

    public function finishRender($code = 0, $msg = '', $params) {
        return $this->controller->render('finish', [
            'storyModel'            => $params['story_model'],
            'sessionModel'          => $params['session_model'],
            'params'        => $_GET,
            'userId'        => $params['user_id'],
            'sessionId'     => $params['session_id'],
            'msg'           => $msg,
        ]);
    }
}