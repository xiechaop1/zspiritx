<?php

namespace ota\globepay\lib;

use yii\base\Exception;

/**
 *
 * GlobePay支付API异常类
 * @author Leijid
 *
 */
class GlobePayException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
