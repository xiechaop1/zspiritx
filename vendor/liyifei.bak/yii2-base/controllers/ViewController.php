<?php
/**
 * Project: fanli
 * User: liyifei
 * Date: 16/2/11
 * Time: 23:38
 */
namespace liyifei\base\controllers;

use Yii;
use yii\web\Cookie;
use yii\web\Response;

class ViewController extends BaseController
{
    public function init()
    {
        parent::init();

        if ($this->enableCsrfValidation && Yii::$app->request->enableCsrfValidation) {
            $this->getView()->registerJs(' $.ajaxSetup({
                         data: {"' . Yii::$app->request->csrfParam . '": "' . Yii::$app->request->getCsrfToken() . '"},
                         cache:false
                    });');
        }
    }

    public function setTitle($title)
    {
        $this->getView()->title = $title;
    }

    public function responseAjax($status, $msg, $type = Response::FORMAT_JSON)
    {
        return $this->response(['status' => $status, 'msg' => $msg], $type);
    }

    public function getCookie($name, $defaultValue = null)
    {
        $cookies = Yii::$app->request->getCookies();
        if ($cookies) {
            return $cookies->getValue($name, $defaultValue);
        }

        return $defaultValue;
    }

    public function setCookie($name, $value, $expire = 0, $domain = '', $path = '/')
    {
        $cookies = Yii::$app->response->getCookies();
        $cookies->add(new Cookie([
            'name' => $name,
            'value' => $value,
            'expire' => time() + $expire,
            'domain' => $domain,
            'path' => $path,
        ]));
    }

    public function unsetCookie($name)
    {
        $cookies = Yii::$app->response->getCookies();
        $cookies->remove($name, true);
    }

    public function destroyCookie()
    {
        $cookies = Yii::$app->response->getCookies();
        $cookies->removeAll();
    }

    public function getSession($name, $defaultValue = null)
    {
        $session = Yii::$app->session;
        return $session->get($name, $defaultValue);
    }

    public function setSession($name, $value)
    {
        $session = Yii::$app->session;
        $session->set($name, $value);
    }

    public function unsetSession($name)
    {
        $session = Yii::$app->session;
        $session->remove($name);
    }

    public function destroySession()
    {
        $session = Yii::$app->session;
        $session->destroy();
    }
}
