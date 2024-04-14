<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\qah5;


use common\definitions\Common;
use common\models\Story;
use common\models\UserModels;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class QaOne extends Action
{

    
    public function run()
    {
        $qaId = Net::get('id');

        if (empty($qaId)) {
            $qaId = Net::get('qa_id');
        }

        if ($qaId) {
            $model = \common\models\Qa::findOne($qaId);
        }

        $style = !empty($_GET['style']) ? $_GET['style'] : 'default';

        if (empty($model)) {
//            $this->controller->render('qaone', [
//                'err_text'  => '没有找到问答信息，请您刷新重试',
//            ]);
            throw new NotFoundHttpException();
        }

//        $response = Yii::$app->chatgpt->callOpenAIChatGPT('你好');
//        $ret['msg'] = $response['choices'][0]['message']['content'];
//        var_dump($response);exit;

        $model = $model->toArray();

        $model['qa_type_name'] = !empty(Qa::$qaType2Name[$model['qa_type']]) ? Qa::$qaType2Name[$model['qa_type']] : '未知';
        $model['story'] = Story::findOne($model['story_id']);

        $model['selected_json'] = \common\helpers\Common::isJson($model['selected']) ? json_decode($model['selected'], true) : $model['selected'];
        $model['attachment'] = \common\helpers\Attachment::completeUrl($model['attachment'], true);

        $rtnAnswerType = 2;
        if ($model['qa_type'] == Qa::QA_TYPE_SELECTION) {
            if (!empty($model['selected_json'])) {
                $newSelectionJson = [];
                foreach ($model['selected_json'] as $m) {
                    if ($m['type'] == 0) {      // Return Answer Type
                        $rtnAnswerType = $m['value'];
                    } else {
                        $newSelectionJson[] = $m;
                    }
                }
                $model['selected_json'] = $newSelectionJson;
            }
        }

        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;

        $keyStoryModels = [];
        if (!empty($model['selected_json'])
            && !empty($model['selected_json']['keyboard'])
            && $model['selected_json']['keyboard'] == 'bagitems'
        ) {
            $keyboardConfig = $model['selected_json'];


            if ($keyboardConfig['keyboard'] == 'bagitems') {
                if (!empty($keyboardConfig['inc_story_model_ids'])) {
                    $incIds = [];
                    $keyStoryModelsConf = [];
                    foreach ($keyboardConfig['inc_story_model_ids'] as $incStoryModelId => $incStoryModelConf) {
                        $incIds[] = $incStoryModelId;
                        $keyStoryModelsConf[$incStoryModelId] = $incStoryModelConf;
                    }
                    $incBagItems = UserModels::find()
                        ->where([
                            'story_model_id' => $incIds,
                            'user_id' => $userId,
                            'session_id' => $sessionId,
                            'is_delete' => \common\definitions\Common::STATUS_NORMAL,
                        ]);
                    if (!empty($storyId)) {
                        $incBagItems = $incBagItems->andFilterWhere(['story_id' => $storyId]);
                    }
                    $incBagItems = $incBagItems->all();

                    if (!empty($incBagItems)) {
                        foreach ($incBagItems as $ibi) {
                            if (!empty($ibi->storyModel)) {
                                $keyStoryModels[] = $ibi->storyModel;
                            }
                        }
                    }
                }
            }
        }

        $tpl = 'qaone';
        if ($style == 'pink') {
            $tpl = 'qaone_pink';
        }

        return $this->controller->render($tpl, [
            'qa'            => $model,
            'params'        => $_GET,
            'userId'        => $userId,
            'storyId'       => $storyId,
            'sessionId'     => $sessionId,
            'sessionStageId'    => $sessionStageId,
            'rtnAnswerType'     => $rtnAnswerType,
            'keyStoryModels'    => $keyStoryModels,
            'style'         => $style,
        ]);
    }
}