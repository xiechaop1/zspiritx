<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/3/7
 * Time: 下午9:20
 */

namespace frontend\controllers;


use frontend\actions\passport\Login;
use liyifei\base\helpers\Net;
use yii\helpers\ArrayHelper;
use common\helpers\Client;
use common\definitions\Member;
use yii\web\Controller;
use yii;

class BaseController extends Controller
{
    public function beforeAction($action)
    {
        //如果未登录，则直接返回
        if(Yii::$app->user->isGuest){
            return $this->goHome();
        }

        $identity = Yii::$app->user->identity;
        if (!empty($identity->web_session_id) &&
            (
                !empty($identity->login_time) && time() - $identity->login_time <= Member::LOGIN_EXPIRE_AT
            )
            &&
            (
                (Client::isMobile() && $identity->wap_session_id != Yii::$app->session->id)
                || (!Client::isMobile() && $identity->web_session_id != Yii::$app->session->id)
            )
        ){
            Yii::$app->user->logout();
            return $this->goHome();
        }

        if (!empty($identity->login_time) && time() - $identity->login_time > Member::LOGIN_EXPIRE_AT ) {
            Yii::$app->user->logout();
            return $this->goHome();
        }

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}