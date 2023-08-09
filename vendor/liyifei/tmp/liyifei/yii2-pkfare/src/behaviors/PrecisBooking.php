<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午11:23
 */

namespace ota\pkfare\behaviors;


class PrecisBooking extends BaseBehavior
{
    public $urlProd = '';

    public $urlDebug = 'https://open.pkfare.com/apitest/preciseBooking';

    public $needDecode = false;

    public function request($data)
    {
        return $this->send([
            'booking' => $data
        ]);
    }
}