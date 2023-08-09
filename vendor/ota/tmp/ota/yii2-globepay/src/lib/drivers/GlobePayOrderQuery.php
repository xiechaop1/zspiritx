<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/20
 * Time: 9:12 PM
 */

namespace ota\globepay\lib\drivers;

use ota\globepay\lib\GlobePayDataBase;


/**
 * 查询订单状态对象
 * @author Leijid
 */
class GlobePayOrderQuery extends GlobePayDataBase
{
    /**
     * 设置商户支付订单号，同一商户唯一
     * @param string $value
     **/
    public function setOrderId($value)
    {
        $this->pathValues['order_id'] = $value;
    }

    /**
     * 获取商户支付订单号
     * @return 值
     **/
    public function getOrderId()
    {
        return $this->pathValues['order_id'];
    }

    /**
     * 判断商户支付订单号是否存在
     * @return true 或 false
     **/
    public function isOrderIdSet()
    {
        return array_key_exists('order_id', $this->pathValues);
    }
}