<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午10:25
 */

namespace ota\pkfare\libs\params;


use yii\base\BaseObject;

class Journey extends BaseObject
{
    public $airline;

    public $flightNum;

    public $arrival;

    public $arrivalDate;

    public $arrivalTime;

    public $departure;

    public $departureDate;

    public $departureTime;

    public $bookingCode;
}