<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 19-4-13
 * Time: 下午8:05
 */

namespace ota\pkfare\behaviors;


class Shopping extends BaseBehavior
{
    public $urlProd = '';

    public $urlDebug = 'https://open.pkfare.com/apitest/shoppingV2';

    public $needDecode = true;

    public function request($data)
    {
        return $this->send(['search' => $data]);
    }
}