<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午10:07
 */

namespace ota\pkfare\behaviors;


class PrecisPricing extends BaseBehavior
{
    public $urlProd = '';

    public $urlDebug = 'https://open.pkfare.com/apitest/precisePricing_V2';

    public $needDecode = false;

    public function request($data)
    {
        return $this->send(['pricing' => $data]);
    }
}