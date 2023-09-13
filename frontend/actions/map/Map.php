<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\map;


use common\definitions\Common;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Map extends Action
{

    
    public function run()
    {
        $get = \Yii::$app->request->get();
        $userId = !empty($get['user_id']) ? $get['user_id'] : 0;

        return $this->controller->render('map', [
            'userId'    => $userId,
        ]);
    }
}