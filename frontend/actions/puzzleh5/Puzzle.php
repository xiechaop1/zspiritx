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

        return $this->controller->render('puzzle', [
            'params'        => $_GET,
            'userId'        => $userId,
            'sessionId'     => $sessionId,
            'sessionStageId'    => $sessionStageId,
            'rows'          => $rows,
            'cols'          => $cols,
            'prefix'        => $prefix,
            'imgWidth'      => $imgWidth,
        ]);
    }
}