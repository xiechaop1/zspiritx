<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 3:14 PM
 */

/**
 * @var \yii\web\View $this ;
 */

/**
 * @var \common\models\Lottery $lottery
 */

\frontend\assets\Lotteryh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '抽奖';

?>
<audio autoplay loop>
  <source src="" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="lottery_id"  value="<?= $lotteryId ?>">
<input type="hidden" name="channel_id" value="<?= $channelId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">

<input type="hidden" name="answer_type" value="2">
<div class="w-100 m-auto">
<audio controls id="audio_right" class="hide">
    <source src="../../static/audio/qa_right.mp3" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>
<audio controls id="audio_wrong" class="hide">
    <source src="../../static/audio/qa_wrong.mp3" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>

    <audio controls id="audio_voice" class="hide">
        <source src="" type="audio/mpeg">
        您的浏览器不支持 audio 元素。
    </audio>
<div class="w-100 m-auto">
    <!--兑奖页面-->
    <div class="show" style="display: block;" >
        <div class="modal-dialog modal-dialog-centered lottery-pink-modal">
            <div class="modal-content modal-lottery-bg">
                <div class="modal-lottery-bg-border">
                    <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width:100%">
                        <div class="m-t-50">
                            <div class="fs-36 text-F6 text-center bold lottery-error-title">
                                <img src="../../static/img/lottery/lottery-tymj.png" class="img-350">
                            </div>
                            <div class="fs-40 text-center  lottery-title bold m-t-50 lottery-pink-content m-t-50">
                                奖品名称XXX
                            </div>

                            <div class="fs-24 m-t-20  text-33 lottery-detail text-center bold lottery-error-title">
                                奖品说明XXX
                            </div>
                            <div class="fs-36 text-F6 text-center bold m-t-50 m-b-20">
                                <label class="btn-pink-m">兑换</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="show" style="display: block;" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-lottery-bg">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 30px;right: 30px;">
                <img src="../../static/img/icon-close.png" class="img-40">
            </span>
                <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                    <div class="m-t-50">
                        <div class="fs-36 text-F6 text-center bold hide lottery-success-title">
                            <img src="../../static/img/bg-lottery-text1.png" class="img-250">
                        </div>
                        <div class="fs-36 text-F6 text-center bold lottery-error-title">
                            <img src="../../static/img/lottery-error-top.png" class="lottery-error-top">
                            <img src="../../static/img/lottery-error.png" class="img-350">
                        </div>

                        <div class="fs-40 text-FF text-center bold m-t-50 lottery-title"></div>
                        <div class="fs-30 text-FF text-center bold m-t-20 lottery-detail"></div>
                        <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">
                            <label class="btn-blue-m">确认</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-dialog modal-dialog-centered lottery-pink-modal">
            <div class="modal-content modal-lottery-bg">
                <div class="modal-lottery-bg-border">
                    <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width:100%">
                        <div class="m-t-50">
                            <div class="fs-36 text-F6 text-center bold lottery-error-title">
                                <img src="../../static/img/lottery/lottery-tymj.png" class="img-350">
                            </div>
                            <div class="fs-36  m-t-50 text-F6 text-center bold lottery-error-title">
                                <img src="../../static/img/lottery/lottery-gift.png" class="img-350">
                            </div>
                            <div class="fs-36  text-33 text-center bold lottery-error-title">
                                <?php
                                if (!empty($userLottery->ct)) {
                                    echo '本抽奖券每张只能使用 <span style="color: yellow">' . $userLottery->ct . '</span> 次';
                                    } else {
                                    echo '本抽奖券已经使用过';
                                }
                                ?>
                                <br>
                                兑换位置：<span style="color: yellow">国家植物园温室大棚</span><br>
                                抽奖活动最终解释权归公园家所有
                            </div>
                            <div class="fs-24 m-t-20  text-66 text-center bold lottery-error-title">
                                 No. <?= !empty($userLottery->lottery_no) ? $userLottery->lottery_no : '' ?>
                            </div>

                            <div class="fs-36 text-F6 text-center bold m-t-50 m-b-20" >
                                        <?php
                                        if (empty($userLottery)) {
                                            ?>

                                                无奖券

                                            <?php
                                        } else {
                                            if ($userLottery->lottery_status == \common\models\UserLottery::USER_LOTTERY_STATUS_WAIT) {
                                            ?>

                                            <label class="btn-pink-m lottery-btn">抽奖</label>
                                            <?php
                                            } else {
                                                ?>

                                                    <?=
                                                    $userLottery->lottery_status == \common\models\UserLottery::USER_LOTTERY_STATUS_USED ? '已抽奖'
                                                        : '已过期/取消'
                                                    ?>

                                                <?php
                                            }
                                        }
                                        ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class="p-20 bg-black hide" style="margin: 0px; padding: 0px;">
        <div class="w-100 p-30" style="margin: 0px; padding: 0px;">
            <div class="w-1-0 d-flex fs-30 bold text-FF">

                <div style="height: 583px;">
<!--                    style="background-image:url(../../static/img/lottery/ticket.jpg); width: 1600px; height: 583px;"-->
                    <img src="../../static/img/lottery/ticket.jpg" width="100%">
<!--                                        <div class="npc-name fs-18" style="padding: 15px 15px; top: 15px; right: 25px;" id="msg_return_btn">-->
<!--                                            X-->
<!--                                        </div>-->

                <div class="fs-18" style="position: absolute; left: 35px; top: 25px;">
                    No. <?= !empty($userLottery->lottery_no) ? $userLottery->lottery_no : '' ?>
                </div>
                    <div class="fs-12" style="position: absolute; left: 50px; top: 200px;">
                        <?php
                        if (!empty($userLottery->ct)) {
                            echo '本抽奖券每张只能使用 <span style="color: yellow">' . $userLottery->ct . '</span> 次';
                            } else {
                            echo '本抽奖券已经使用过';
                        }
                        ?>
                        <br>
                        兑换位置：<span style="color: yellow">国家植物园温室大棚</span><br>
                        抽奖活动最终解释权归公园家所有
                    </div>
                    <div style="position: absolute; left: 370px; top: 180px;">
                                        <?php
                                        if (empty($userLottery)) {
                                            ?>
                                            <label id="answer-info" style="background-color: #a61717; color:#a0a0a0" class="h5-btn-green-big lottery-btn">
                                                无奖券
                                            </label>

                                            <?php
                                        } else {
                                            if ($userLottery->lottery_status == \common\models\UserLottery::USER_LOTTERY_STATUS_WAIT) {
                                            ?>
                                            <label id="answer-info" style="background-color: #a61717" class="h5-btn-green-big lottery-btn">
                                                抽奖
                                            </label>
                                            <?php
                                            } else {
                                                ?>
                                                <label id="answer-info" style="background-color: #a61717; color:#a0a0a0" class="h5-btn-green-big lottery-btn">
                                                    <?=
                                                    $userLottery->lottery_status == \common\models\UserLottery::USER_LOTTERY_STATUS_USED ? '已抽奖'
                                                        : '已过期/取消'
                                                    ?>
                                                </label>
                                                <?php
                                            }
                                        }
                                        ?>
                    </div>
                </div>

                <!--                <div class="fs-30 bold w-100 text-FF title-box-border">-->
<!--                    <div class="npc-name">-->
<!--                        抽奖-->
<!--                    </div>-->
<!--                    <div class="npc-name" style="right: 60px;" id="msg_return_btn">-->
<!--                        X-->
<!--                    </div>-->
<!--                    --><?php //= !empty($userLottery->lottery->lottery_name) ? $userLottery->lottery->lottery_name : '' ?><!-- 抽奖-->
<!--                    <div>-->
<!--                        奖券--><?php //= !empty($userLottery->lottery_no) ? $userLottery->lottery_no : '' ?>
<!--                    </div>-->
<!--                    --><?php
//                    if ($userLottery->lottery_status == \common\models\UserLottery::USER_LOTTERY_STATUS_WAIT) {
//                    ?>
<!--                    <label id="answer-info" class="h5-btn-green-big lottery-btn">-->
<!--                        抽奖-->
<!--                    </label>-->
<!--                    --><?php
//                    } else {
//                        ?>
<!--                        <label id="answer-info" class="h5-btn-green-big lottery-btn">-->
<!--                            --><?php //=
//                            $userLottery->lottery_status == \common\models\UserLottery::USER_LOTTERY_STATUS_USED ? '已抽奖'
//                                : '已过期/取消'
//                            ?>
<!--                        </label>-->
<!--                        --><?php
//                    }
//                    ?>
<!--                    <div class="hpa-ctr">-->
<!--                        <img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
<!--                        播放语音-->
<!--                    </div>-->
                </div>
            </div>

        </div>

    </div>

</div>


<!-- 按钮：抽奖结果显示 -->
<div class="modal fade" id="lottery-success" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-lottery-bg">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 30px;right: 30px;">
                <img src="../../static/img/icon-close.png" class="img-40">
            </span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div class="m-t-50">
                    <div class="fs-36 text-F6 text-center bold hide lottery-success-title">
                        <img src="../../static/img/bg-lottery-text1.png" class="img-250">
                    </div>
                    <div class="fs-36 text-F6 text-center bold lottery-error-title">
                        <img src="../../static/img/lottery-error-top.png" class="lottery-error-top">
                        <img src="../../static/img/lottery-error.png" class="img-350">
                    </div>

                    <div class="fs-40 text-FF text-center bold m-t-50 lottery-title"></div>
                    <div class="fs-30 text-FF text-center bold m-t-20 lottery-detail"></div>
                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">
                        <label class="btn-blue-m">确认</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-null" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                       请选择答案
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-right" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        恭喜您答对了
                    </div>
                    <div class="text-center m-t-30">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="lottery-success-pink" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered lottery-pink-modal">
        <div class="modal-content modal-lottery-bg">
            <div class="modal-lottery-bg-border">
                <span class="close m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal">
                <img src="../../static/img/icon-close.png" class="img-40">
            </span>
                <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width:100%">
                    <div class="m-t-50">
                        <div class="fs-36 text-F6 text-center bold lottery-error-title">
                            <img src="../../static/img/lottery/lottery-tymj.png" class="img-350">
                        </div>
                        <div class="fs-40 text-center  lottery-title bold m-t-50 lottery-pink-content m-t-50">

                        </div>

                        <div class="fs-24 m-t-20  text-33 lottery-detail text-center bold lottery-error-title">

                        </div>
                        <div class="fs-36 text-F6 text-center bold m-t-50 m-b-20">
                            <label class="btn-pink-m">兑换</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-worry" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        很遗憾，打错了
                    </div>
                    <div class="m-t-40 bg-F5 p-20 fs-26 text-orange border-radius-r-5 border-radius-l-5">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
