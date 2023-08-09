<?php
/**
 * Created by PhpStorm.
 * User: yifei
 * Date: 2019/3/18
 * Time: 23:24
 */

namespace ota\globepay\lib\drivers;



/**
 * 线下QRCode支付单
 */
class GlobePayRetailQRCode extends GlobePayUnifiedOrder
{
    /**
     * 设置设备ID
     * @param string $value
     **/
    public function setDeviceId($value)
    {
        $this->bodyValues['device_id'] = $value;
    }

    /**
     * 获取设备ID
     * @return 值
     **/
    public function getDeviceId()
    {
        return $this->bodyValues['device_id'];
    }

    /**
     * 判断设备ID是否存在
     * @return true 或 false
     **/
    public function isDeviceIdSet()
    {
        return array_key_exists('device_id', $this->bodyValues);
    }
}