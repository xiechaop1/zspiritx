<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2018/2/11
 * Time: 上午10:36
 */

namespace liyifei\base\actions;


use liyifei\base\definitions\Api;
use yii\base\Action;
use yii;

class ApiAction extends Action
{

    public $page;

    public $load_all;

    public $data;

    public $pagedData = [
        'totalCount' => 0,
        'totalPage' => 0,
        'list' => []
    ];

    public function init()
    {
        parent::init();

        $this->page = Yii::$app->request->get('page', 0);
        if (!$this->page) {
            $this->page = Yii::$app->request->post('page', 0);
        }

        $this->load_all = Yii::$app->request->get('load_all', 0);
        if (!$this->load_all) {
            $this->load_all = Yii::$app->request->post('load_all', 0);
        }
    }

    public function success($data = null)
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        return [
            'status' => true,
            'code' => 200,
            'msg' => '',
            'data' => $data
        ];
    }

    public function fail($errorMessage, $errorCode = Api::REQUEST_FAIL)
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        return [
            'status' => false,
            'code' => $errorCode,
            'msg' => $errorMessage,
            'data' => null
        ];
    }

}