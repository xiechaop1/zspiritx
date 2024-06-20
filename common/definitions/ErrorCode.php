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

    const PARAMS_ERROR = -1001;      // 参数错误

    const SMS_FAILED = -1010;

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

    const USER_MODEL_NO_TARGET      = -80004;   // 用户模型没有目标
    const USER_MODEL_NOT_ALLOW      = -80005;   // 用户模型不允许使用

    const USER_MODEL_NO_EFFECT      = -80006;   // 用户模型没有效果

    const USER_KNOWLEDGE_NOT_FOUND      = -90001;   // 知识不存在
    const USER_KNOWLEDGE_OPERATE_FAILED = -90002;   // 知识操作失败

    const USER_PRIZE_NOT_FOUND = -100001; // 用户奖品不存在

    const USER_PRIZE_STATUS_ERROR = -100002; // 用户奖品状态错误

    const USER_PRIZE_AWARD_METHOD_ERROR = -100003; // 用户奖品领取方式错误

    const USER_PRIZE_EXPIRED = -100004; // 用户奖品已过期

    const USER_PRIZE_CANCEL = -100005; // 用户奖品已取消

    const USER_PRIZE_RECEIVED = -100006; // 用户奖品已领取

    const USER_PRIZE_NOT_ALLOW = -100007; // 用户奖品不允许领取

    const USER_PRIZE_NOT_ENOUGH = -100008; // 用户奖品不足

    const USER_PRIZE_AWARD_METHOD_NOT_ALLOW = -100009; // 用户奖品领取方式不允许

    const USER_PRIZE_AWARD_METHOD_NOT_FOUND = -100010; // 用户奖品领取方式不存在

    const USER_PRIZE_ADD_FAILED = -100011; // 用户奖品添加失败

    const USER_LOTTERY_NOT_FOUND = -110001; // 用户抽奖券不存在

    const USER_LOTTERY_ADD_FAILED = -110002; // 用户抽奖券添加失败

    const STORY_MATCH_ALREADY_EXIST_READY = -120002; // 已经有待比赛的车辆

    const STORY_MATCH_NOT_EXIST_READY = -120003; // 没有准备参赛的汽车

    const STORY_MATCH_NOT_MODEL_READY = -120004; // 没有准备参赛的模型

    const STORY_MATCH_NOT_READY = -120005; // 比赛未准备好

    const SHOP_BUY_FAILED = -130000; // 商店购买失败
    const SHOP_BUY_NOT_ENOUGH_SCORE = -130001; // 商店购买失败，金币不足
    const SHOP_WARE_NOT_EXIST = -130002; // 商店商品不存在

}