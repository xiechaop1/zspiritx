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

class QaRandom extends Action
{

    
    public function run()
    {
        $qaId = Net::get('id');

        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $storyId = !empty($_GET['story_id']) ? $_GET['story_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;

        $storyModelId = !empty($_GET['story_model_id']) ? $_GET['story_model_id'] : 0;

        if (empty($qaId)) {
            $qaId = Net::get('qa_id');
        }

        if ($qaId) {
            $model = \common\models\Qa::findOne($qaId);
        }

        if (empty($qaId)) {
            $qaRandomIds = Net::get('qa_random_ids');
            if ($qaRandomIds) {
                $qaRandomIds = explode(',', $qaRandomIds);
                $qaId = $qaRandomIds[array_rand($qaRandomIds)];
                $model = \common\models\Qa::findOne($qaId);
            }
        }

        if (empty($model)) {
//            $isRandom = Net::get('is_random');
//            if ($isRandom) {
                $model = \common\models\Qa::find()
                    ->where([
                        'qa_class' => Qa::QA_CLASS_RANDOM,
//                        'is_delete' => Common::STATUS_NORMAL,
                    ])
                    ->orderBy('rand()')
                    ->one();
//            }
        }

        $style = !empty($_GET['style']) ? $_GET['style'] : 'default';

        if (empty($model)) {
//            $this->controller->render('qaone', [
//                'err_text'  => '没有找到问答信息，请您刷新重试',
//            ]);
            throw new NotFoundHttpException();
        }

        switch ($model->qa_type) {
            case Qa::QA_TYPE_PUZZLE_PIC:
                $uri = '/puzzleh5/puzzle';
                $selectedJson = json_decode($model->selected, true);
                $params = [
                    'rows' => !empty($selectedJson['rows']) ? $selectedJson['rows'] : 0,
                    'cols' => !empty($selectedJson['cols']) ? $selectedJson['cols'] : 0,
                    'img_width' => !empty($selectedJson['imgWidth']) ? $selectedJson['imgWidth'] : 0,
                    'prefix' => !empty($selectedJson['prefix']) ? $selectedJson['prefix'] : '',
                    'qa_id' => $qaId,
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'session_stage_id' => $sessionStageId,
                    'story_id' => $storyId,
                    ];

                break;
            case Qa::QA_TYPE_SECRET:
                $uri = '/secreth5/secret';
                $params = [
                    'qa_id' => $qaId,
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'session_stage_id' => $sessionStageId,
                    'story_id' => $storyId,
                    'story_model_id' => $storyModelId,
                ];
                break;
            case QA::QA_TYPE_SUDOKU:
                $uri = '/puzzleh5/puzzle_sudoku';
//                $selectedJson = json_decode($model->selected, true);
                $params = [
//                    'size' => !empty($selectedJson['size']) ? $selectedJson['size'] : 0,
//                    'hole' => !empty($selectedJson['hole']) ? $selectedJson['hole'] : 0,
                    'qa_id' => $qaId,
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'session_stage_id' => $sessionStageId,
                ];
                break;
            default:
                $uri = '/qah5/qa_one';
                $params = [
                    'id' => $qaId,
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'session_stage_id' => $sessionStageId,
                    'story_id' => $storyId,
                ];
                break;

        }
        $url = $uri . '?' . http_build_query($params);

        header('location: ' . $url);

//        $response = Yii::$app->chatgpt->callOpenAIChatGPT('你好');
//        $ret['msg'] = $response['choices'][0]['message']['content'];
//        var_dump($response);exit;


    }
}