<?php
/**
 * Created by PhpStorm.
 * User: liyifei
 * Date: 2019/4/14
 * Time: 下午10:58
 */

namespace ota\pkfare\libs\params;


use yii\base\BaseObject;

class PreciseBookingPassenger extends BaseObject
{
    public $birthday;

    /**
     * P 护照
     * N 身份证
     * O 其他
     */
    public $cardType;

    public $cardNum;

    public $cardExpiredDate;

    public $lastName;

    public $firstName;

    public $nationality;

    /**
     * ADT 成人
     * CHD 儿童
     */
    public $psgType;

    /**
     * M 男
     * F 女
     */
    public $sex;
}