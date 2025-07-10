<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/25
 * Time: 1:51 PM
 */

namespace frontend\actions\jncity;


use common\models\UserBook;
use yii\base\Action;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class Poi extends Action
{

    private $_get;

    private $_params;
    
    public function run()
    {
        $this->_get = Yii::$app->request->get();

        $poiList = UserBook::$poiList;

        return $this->controller->render('menu', [
            'params'        => $_GET,
            'poi_list'      => $poiList,
        ]);

    }

}