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

    const SESSION_NOT_FOUND         = -40001;   // 场次不存在

    const SESSION_PASSWORD_ERROR    = -40002;   // 场次密码错误

    const ROLE_NOT_FOUND            = -50001;   // 角色不存在

    const ROLE_FULL                 = -50002;   // 角色已满

    const PLAYER_EXIST              = -50003;   // 玩家已存在

    const DO_PRE_MODELS_NOT_FOUND   = -60001;   // 没有前置模型

    const DO_MODELS_PICK_UP_FAIL    = -60002;   // 模型拾取失败

    const QA_NOT_EXIST              = -70001;   // 问答不存在

    const QA_SAVE_FAILED            = -70002;   // 问答保存失败

    const USER_MODEL_NOT_FOUND      = -80001;   // 用户模型不存在
    const USER_MODEL_NOT_ENOUGH     = -80002;   // 用户模型不足
    const USER_MODEL_BUFF_NOT_FOUND = -80003;   // 用户模型buff不存在

    const USER_KNOWLEDGE_NOT_FOUND      = -90001;   // 知识不存在
    const USER_KNOWLEDGE_OPERATE_FAILED = -90002;   // 知识操作失败

}