<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\myh5;


use common\definitions\Common;
use common\definitions\ErrorCode;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\helpers\Model;
use common\models\LotteryPrize;
use common\models\Order;
use common\models\Poem;
use common\models\Story;
use common\models\StoryMatch;
use common\models\StoryMatchPlayer;
use common\models\StoryRank;
use common\models\User;
use common\models\UserExtends;
use common\models\UserLottery;
use common\models\UserModelLoc;
use common\models\UserModels;
use common\models\UserPrize;
use common\models\UserQa;
use common\models\UserScore;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Wrong extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $channelId = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
//        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
//        $matchId = !empty($_GET['match_id']) ? $_GET['match_id'] : 0;

        $qaType = !empty($_GET['qa_type']) ? $_GET['qa_type'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;
        $page = !empty($_GET['page']) ? $_GET['page'] : 1;
        $limit = 10;
//        $userModelId = !empty($_GET['user_model_id']) ? $_GET['user_model_id'] : 0;

        $userQa = UserQa::find()
            ->joinWith('qa')
            ->where([
                'o_user_qa.user_id'   => $userId,
                'is_right'  => UserQa::ANSWER_WRONG,
            ]);
        if (!empty($qaType)) {
            $userQa = $userQa->andFilterWhere([
                    'o_qa.qa_type'    => $qaType,
                ]);
        }
        $userQa = $userQa->orderBy('id desc')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->all();

        $allCt = UserQa::find()
            ->where([
                'o_user_qa.user_id'   => $userId,
                'is_right'  => UserQa::ANSWER_WRONG,
            ])->count();

        if (!empty($userQa)) {
            foreach ($userQa as &$uqa) {
                $qa = $uqa->qa;
                $qa = $qa->toArray();
                $qa['selected_json'] = \common\helpers\Common::isJson($qa['selected']) ? json_decode($qa['selected'], true) : $qa['selected'];
            }
        }

        $user = User::find()
            ->where([
                'id'    => $userId,
            ])
            ->one();

        if (!empty($user['avatar'])) {
            $user['avatar'] = Attachment::completeUrl($user['avatar']);
        } else {
            $user['avatar'] = 'https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/05/x74pyndc2mwx8ppkrb4b88jzk5yrsxff.png?x-oss-process=image/format,png';
        }

        $userExtends = UserExtends::find()
            ->where([
                'user_id'   => $userId,
            ])
            ->one();

        $level = 0;
        if (!empty($userExtends['level'])) {
            $level = $userExtends['level'];
        }

        if (empty($user)) {
            return $this->renderErr('用户不存在！');
        }


        $subjects = [];
        if (!empty($userQa)) {
            foreach ($userQa as $uqa) {
                $subjects[] = [
                    'qa' => \common\helpers\Qa::formatSubjectFromUserQa($uqa),
                    'user_qa' => $uqa->toArray(),
                ];
            }
        }

        return $this->controller->render('wrong', [
            'params'        => $_GET,
            'userId'        => $userId,
            'storyId'       => $storyId,
            'subjects'      => $subjects,
            'level'         => $level,
//            'qa'            => $qa,
            'rtnAnswerType' => 2,
            'subjectsJson' => json_encode($subjects, JSON_UNESCAPED_UNICODE),
            'ct'            => $allCt,
            'user' => $user,

        ]);
    }



    public function renderErr($errTxt) {
        return $this->controller->render('msg', [
            'msg' => $errTxt,
        ]);
    }
}