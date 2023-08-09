<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/4/17
 * Time: 5:22 PM
 */

namespace ota\pkfare\behaviors;


class Ticketing extends BaseBehavior
{
    public $urlProd = '';

    public $urlProdPath = 'ticketing';

    public $urlDebug = 'https://open.pkfare.com/apitest/ticketing';

    public function request($data)
    {
        return $this->send([
            'ticketing' => $data
        ]);
    }
}