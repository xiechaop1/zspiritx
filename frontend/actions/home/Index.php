<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\home;


use common\definitions\Common;
use common\helpers\Attachment;
use common\models\Story;
use yii\base\Action;
use kartik\form\ActiveForm;
use liyifei\base\helpers\Net;
use common\models\Qa;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Index extends Action
{

    
    public function run()
    {

        $image = 'img/home/index_image.jpg';
        $image = Attachment::completeUrl($image, true);

        return $this->controller->render('index', [
            'userId'    => 1,
            'voice' => '',
            'image' => $image,
        ]);
    }
}