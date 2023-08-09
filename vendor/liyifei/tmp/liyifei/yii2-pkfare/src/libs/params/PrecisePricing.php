<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午10:24
 */

namespace ota\pkfare\libs\params;


use yii\base\BaseObject;

class PrecisePricing extends BaseObject
{
    public $adults;

    public $children;

    public $cabin = '';

    public $journeys;
}