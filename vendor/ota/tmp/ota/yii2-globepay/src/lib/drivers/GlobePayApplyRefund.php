<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/20
 * Time: 8:34 PM
 */

namespace ota\globepay\lib\drivers;


use ota\globepay\lib\GlobePayDataBase;

/**
 * 申请退款对象
 * @author Leijid
 */
class GlobePayApplyRefund extends GlobePayDataBase
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

    /**
     * 设置商户退款单号
     * @param string $value
     **/
    public function setRefundId($value)
    {
        $this->pathValues['refund_id'] = $value;
    }

    /**
     * 获取商户退款单号
     * @return 值
     **/
    public function getRefundId()
    {
        return $this->pathValues['refund_id'];
    }

    /**
     * 判断商户退款单号是否存在
     * @return true 或 false
     **/
    public function isRefundIdSet()
    {
        return array_key_exists('refund_id', $this->pathValues);
    }

    /**
     * 设置退款金额，单位是货币最小单位
     * @param string $value
     **/
    public function setFee($value)
    {
        $this->bodyValues['fee'] = $value;
    }

    /**
     * 获取退款金额
     * @return 值
     **/
    public function getFee()
    {
        return $this->bodyValues['fee'];
    }

    /**
     * 判断退款金额是否存在
     * @return true 或 false
     **/
    public function isFeeSet()
    {
        return array_key_exists('fee', $this->bodyValues);
    }
}