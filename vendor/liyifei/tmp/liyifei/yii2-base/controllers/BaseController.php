<?php
/**
 * Project: fanli
 * User: liyifei
 * Date: 16/2/11
 * Time: 23:39
 */
namespace liyifei\base\controllers;


use Yii;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller{
    public function response($data, $type = Response::FORMAT_JSON)
    {
        Yii::$app->response->format = $type;

        return $data;
    }
}
