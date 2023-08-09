<?php

namespace ota\globepay\lib;


use ota\globepay\lib\drivers\GlobePayUnifiedOrder;
use yii\base\Component;

/**
 *
 * 数据对象基础类，该类中定义数据类最基本的行为，包括：
 * 计算/设置/获取签名、输出json格式的参数、从json读取数据对象等
 * @author Leijid
 *
 */
class GlobePayDataBase extends Component
{
    public $partnerCode;

    public $credentialCode;

    protected $pathValues = array();

    protected $queryValues = array();

    protected $bodyValues = array();


    /**
     * 设置随机字符串，不长于30位。推荐随机数生成算法
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->queryValues['nonce_str'] = $value;
    }

    /**
     * 获取随机字符串，不长于30位。推荐随机数生成算法的值
     * @return 值
     **/
    public function getNonceStr()
    {
        return $this->queryValues['nonce_str'];
    }

    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     * @return true 或 false
     **/
    public function isNonceStrSet()
    {
        return array_key_exists('nonce_str', $this->queryValues);
    }

    /**
     * 设置时间戳
     * @param long $value
     **/
    public function setTime($value)
    {
        $this->queryValues['time'] = $value;
    }

    /**
     * 获取时间戳
     * @return 值
     **/
    public function getTime()
    {
        return $this->queryValues['time'];
    }

    /**
     * 判断时间戳是否存在
     * @return true 或 false
     **/
    public function isTimeSet()
    {
        return array_key_exists('time', $this->queryValues);
    }

    /**
     * 设置签名，详见签名生成算法
     * @param string $value
     **/
    public function setSign()
    {
        $sign = $this->makeSign();
        $this->queryValues['sign'] = $sign;
        return $sign;
    }

    /**
     * 获取签名，详见签名生成算法的值
     * @return 值
     **/
    public function getSign()
    {
        return $this->queryValues['sign'];
    }

    /**
     * 判断签名，详见签名生成算法是否存在
     * @return true 或 false
     **/
    public function isSignSet()
    {
        return array_key_exists('sign', $this->queryValues);
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function toQueryParams()
    {
        $buff = "";
        foreach ($this->queryValues as $k => $v) {
            if ($v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 格式化参数格式化成json参数
     */
    public function toBodyParams()
    {
        return json_encode($this->bodyValues);
    }

    /**
     * 格式化签名参数
     */
    public function toSignParams()
    {
        $buff = "";
        $buff .= $this->partnerCode . '&' . $this->getTime() . '&' . $this->getNonceStr() . "&" . $this->credentialCode;
        return $buff;
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用setSign方法赋值
     */
    public function makeSign()
    {
        //签名步骤一：构造签名参数
        $string = $this->toSignParams();
        //签名步骤三：SHA256加密
        $string = hash('sha256', utf8_encode($string));
        //签名步骤四：所有字符转为小写
        $result = strtolower($string);
        return $result;
    }

    /**
     * 获取设置的path参数值
     */
    public function getPathValues()
    {
        return $this->pathValues;
    }

    /**
     * 获取设置的query参数值
     */
    public function getQueryValues()
    {
        return $this->queryValues;
    }

    /**
     * 获取设置的body参数值
     */
    public function getBodyValues()
    {
        return $this->bodyValues;
    }
}

/**
 *
 * 接口调用结果类
 * @author Leijid
 *
 */
class GlobePayResults extends GlobePayDataBase
{

    /**
     *
     * 使用数组初始化
     * @param array $array
     */
    public function fromArray($array)
    {
        $this->bodyValues = json_decode($array, true);
    }

    /**
     * 将json转为array
     * @param string $json
     * @throws GlobePayException
     *
     * 返回信息:
     * return_code          return_msg
     * --------------------------------------
     * ORDER_NOT_EXIST      订单不存在
     * ORDER_MISMATCH       订单号与商户不匹配
     * SYSTEMERROR          系统内部异常
     * INVALID_SHORT_ID     商户编码不合法或没有对应商户
     * SIGN_TIMEOUT         签名超时，time字段与服务器时间相差超过5分钟
     * INVALID_SIGN         签名错误
     * PARAM_INVALID        参数不符合要求，具体细节可参考return_msg字段
     * NOT_PERMITTED        未开通网关支付权限
     * --------------------------------------
     */
    public static function prepare($array)
    {
        $obj = new self();
        $obj->fromArray($array);
        return $obj->getBodyValues();
    }
}


/**
 * QRCode支付跳转对象
 * @author Leijid
 */
class GlobePayRedirect extends GlobePayDataBase
{
    /**
     * 设置支付成功后跳转页面
     * @param string $value
     **/
    public function setRedirect($value)
    {
        $this->queryValues['redirect'] = $value;
    }

    /**
     * 获取支付成功后跳转页面
     * @return 值
     **/
    public function getRedirect()
    {
        return $this->queryValues['redirect'];
    }

    /**
     * 判断支付成功后跳转页面是否存在
     * @return true 或 false
     **/
    public function isRedirectSet()
    {
        return array_key_exists('redirect', $this->queryValues);
    }
}

/**
 * jsapi支付跳转对象
 * @author Leijid
 */
class GlobePayJsApiRedirect extends GlobePayRedirect
{
    /**
     * 设置是否直接支付
     * @param string $value
     **/
    public function setDirectPay($value)
    {
        $this->queryValues['directpay'] = $value;
    }

    /**
     * 获取是否直接支付
     * @return 值
     **/
    public function getDirectPay()
    {
        return $this->queryValues['directpay'];
    }

    /**
     * 判断直接支付是否存在
     * @return true 或 false
     **/
    public function isDirectPaySet()
    {
        return array_key_exists('directpay', $this->queryValues);
    }
}


/**
 * 查询退款状态对象
 * @author Leijid
 */
class GlobePayQueryRefund extends GlobePayDataBase
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
}

/**
 * 查询退款状态对象
 * @author Leijid
 */
class GlobePayQueryOrders extends GlobePayDataBase
{
    /**
     * 设置订单创建日期，'yyyyMMdd'格式，澳洲东部时间，不填默认查询所有订单
     * @param string $value
     **/
    public function setDate($value)
    {
        $this->queryValues['date'] = $value;
    }

    /**
     * 获取订单创建日期
     * @return 值
     **/
    public function getDate()
    {
        return $this->queryValues['date'];
    }

    /**
     * 判断订单创建日期是否存在
     * @return true 或 false
     **/
    public function isDateSet()
    {
        return array_key_exists('date', $this->queryValues);
    }

    /**
     * 设置订单状态
     * ALL:全部订单，包括未完成订单和已关闭订单
     * PAID:只列出支付过的订单，包括存在退款订单
     * REFUNDED:只列出存在退款订单
     * 默认值: ALL
     * 允许值: 'ALL', 'PAID', 'REFUNDED'
     * @param string $value
     **/
    public function setStatus($value = 'ALL')
    {
        $this->queryValues['status'] = $value;
    }

    /**
     * 获取订单状态
     * @return 值
     **/
    public function getStatus()
    {
        return $this->queryValues['status'];
    }

    /**
     * 判断订单状态是否存在
     * @return true 或 false
     **/
    public function isStatusSet()
    {
        return array_key_exists('status', $this->queryValues);
    }

    /**
     * 设置页码，从1开始计算
     * 默认值: 1
     * @param int $value
     **/
    public function setPage($value = 1)
    {
        $this->queryValues['page'] = $value;
    }

    /**
     * 获取页码
     * @return 值
     **/
    public function getPage()
    {
        return $this->queryValues['page'];
    }

    /**
     * 判断页码是否存在
     * @return true 或 false
     **/
    public function isPageSet()
    {
        return array_key_exists('page', $this->queryValues);
    }

    /**
     * 设置每页条数
     * 默认值: 10
     * @param int $value
     **/
    public function setLimit($value = 10)
    {
        $this->queryValues['limit'] = $value;
    }

    /**
     * 获取每页条数
     * @return 值
     **/
    public function getLimit()
    {
        return $this->queryValues['limit'];
    }

    /**
     * 判断每页条数是否存在
     * @return true 或 false
     **/
    public function isLimitSet()
    {
        return array_key_exists('limit', $this->queryValues);
    }
}