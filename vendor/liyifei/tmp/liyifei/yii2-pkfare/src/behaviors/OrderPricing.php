<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 4:14 PM
 */

namespace ota\pkfare\behaviors;


class OrderPricing extends BaseBehavior
{
    public $urlProd = '';

    public $urlProdPath = 'orderPricing';

    public $urlDebug = 'https://open.pkfare.com/apitest/orderPricing';

    public function request($data)
    {
        return $this->send([
            'orderPricing' => $data
        ]);
    }
}