<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\puzzleh5;


use common\definitions\Common;
use common\helpers\Attachment;
use common\helpers\Client;
use common\helpers\Cookie;
use common\models\Order;
use common\models\Story;
use common\models\User;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Puzzle extends Action
{

    
    public function run()
    {
        $userId = !empty($_GET['user_id']) ? $_GET['user_id'] : 0;
        $sessionId = !empty($_GET['session_id']) ? $_GET['session_id'] : 0;
        $sessionStageId = !empty($_GET['session_stage_id']) ? $_GET['session_stage_id'] : 0;

        $rows = !empty($_GET['rows']) ? $_GET['rows'] : 0;
        $cols = !empty($_GET['cols']) ? $_GET['cols'] : 0;
        $imgWidth = !empty($_GET['img_width']) ? $_GET['img_width'] : 0;
        $prefix = !empty($_GET['prefix']) ? $_GET['prefix'] : 0;

        $qaId = !empty($_GET['qa_id']) ? $_GET['qa_id'] : 0;

        $qaOne = Qa::find()
            ->where([
                'id'    => $qaId,
            ])
            ->one();

        if (empty($qaOne)) {
            throw new NotFoundHttpException('QA not found');
        }
        $qaOne = $qaOne->toArray();
        $qaOne['selected_json'] = \common\helpers\Common::isJson($qaOne['selected']) ? json_decode($qaOne['selected'], true) : $qaOne['selected'];
        $qaOne['attachment'] = \common\helpers\Attachment::completeUrl($qaOne['attachment'], true);

        $rows = !empty($qaOne['selected_json']['rows']) ? $qaOne['selected_json']['rows'] : $rows;
        $cols = !empty($qaOne['selected_json']['cols']) ? $qaOne['selected_json']['cols'] : $cols;
        $imgWidth = !empty($qaOne['selected_json']['imgWidth']) ? $qaOne['selected_json']['imgWidth'] : $imgWidth;
        $prefix = !empty($qaOne['selected_json']['prefix']) ? $qaOne['selected_json']['prefix'] : $prefix;


        return $this->controller->render('puzzle', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'    => $sessionStageId,
            'qaId'          => $qaId,
            'storyId'       => $qaOne['story_id'],
            'rows'          => $rows,
            'cols'          => $cols,
            'prefix'        => $prefix,
            'imgWidth'      => $imgWidth,
        ]);
    }
}