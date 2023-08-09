<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 9:24 AM
 */

namespace ota\pkfare\behaviors;


class CancelOrder extends BaseBehavior
{
    public $urlProd = '';

    public $urlProdPath = 'cancel';

    public $urlDebug = 'https://open.pkfare.com/apitest/cancel';

    public function request($data)
    {
        return $this->send([
            'cancel' => $data
        ]);
    }
}