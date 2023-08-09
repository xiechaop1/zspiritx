<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/15
 * Time: 4:33 PM
 */

namespace ota\pkfare\behaviors;


class Penalty extends BaseBehavior
{
    public $urlProd = '';

    public $urlDebug = 'https://open.pkfare.com/apitest/penalty';

    public $needDecode = false;

    public function request($data)
    {
        return $this->send([
            'penalty' => $data
        ]);
    }
}