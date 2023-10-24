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
        if ($qaId) {
            $model = \common\models\Qa::findOne($qaId);
        }

        if (empty($model)) {
//            $this->controller->render('qaone', [
//                'err_text'  => '没有找到问答信息，请您刷新重试',
//            ]);
            throw new NotFoundHttpException();
        }

        $model = $model->toArray();

        $model['qa_type_name'] = !empty(Qa::$qaType2Name[$model['qa_type']]) ? Qa::$qaType2Name[$model['qa_type']] : '未知';
        $model['story'] = Story::findOne($model['story_id']);

        $model['selected_json'] = \common\helpers\Common::isJson($model['selected']) ? json_decode($model['selected'], true) : $model['selected'];
        $model['attachment'] = \common\helpers\Attachment::completeUrl($model['attachment'], true);

        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;

        return $this->controller->render('qaone', [
            'qa'            => $model,
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'    => $sessionStageId,
        ]);
    }
}