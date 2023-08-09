<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/16
 * Time: 下午9:07
 */

namespace ota\pkfare\libs\params;


use yii\base\BaseObject;

class PreciseBookingSolution extends BaseObject
{
    public $fareType;

    public $adtFare;

    public $adtTax;

    public $chdFare;

    public $chdTax;

    public $comments;

    public $journeys;
}