<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/19
 * Time: 4:08 PM
 */

namespace ota\globepay;


use ota\globepay\lib\GlobePayDataBase;
use yii\base\Action;
use yii\web\BadRequestHttpException;

class NotifyAction extends Action
{
    public $decoded;

    public $handler;

    /**
     * @var \ota\globepay\Application $application
     */
    public $application;

    public function init()
    {
        $this->decoded = json_decode(file_get_contents("php://input"), true);

        $input = new GlobePayDataBase([
            'partnerCode' => $this->application->partnerCode,
            'credentialCode' => $this->application->credentialCode
        ]);
        $input->setNonceStr($this->decoded['nonce_str']);
        $input->setTime($this->decoded['time']);
        $input->setSign();

        if ($input->getSign() != $this->decoded['sign']) {
            throw new BadRequestHttpException();
        }

        parent::init();
    }

    public function run()
    {
        return call_user_func_array($this->handler, [$this->decoded]);
    }
}