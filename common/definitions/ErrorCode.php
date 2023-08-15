<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2018/5/28
 * Time: 下午3:51
 */

namespace common\definitions;


use liyifei\base\definitions\Api;

class ErrorCode extends Api
{
    const SUCCESS = 0;              // 成功

    const UNKNOWN_ERROR = -1000;     // 未知错误

    const USER_PARAMETERS_INVALID   = -10001;   // 用户参数校验失败

    const USER_NOT_FOUND            = -10002;   // 用户不存在

    const USER_REGISTER_FAIL        = -10003;   // 用户注册失败

    const USER_EXIST                = -10004;   // 用户已存在
    const USER_FORBIDDEN            = -10005;   // 用户被禁用

    const ORDER_NOT_FOUND           = -30001;   // 订单不存在

    const ORDER_STATUS_ERROR        = -30002;   // 订单状态错误

    const STORY_NOT_FOUND           = -20001;   // 剧本不存在
}