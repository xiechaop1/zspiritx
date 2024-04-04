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
    <label class="close-btn hide">
        <img src="../../img/icon-close.png" class="img-40">
    </label>
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
                                <?= !empty($userPrize->prize->prize_name) ? $userPrize->prize->prize_name : ' - ' ?>
                            </div>

                            <div class="fs-24 m-t-20  text-33 lottery-detail text-center bold lottery-error-title">
                                兑奖码：<?= !empty($userPrize->user_prize_no) ? $userPrize->user_prize_no : ' - ' ?><br><br>
                                本次活动奖品由公园加提供<br>
                                活动兑奖地址：科普馆门口公园加活动区<br>
                                兑换时间：20204.4.4-2024.5.4<br>
                                兑换方式：请到现场联系工作人员兑换<br>
                                本次活动最终解释权归公园加所有<br>
                            </div>
                            <div class="fs-36 text-F6 text-center bold m-t-50 m-b-20">
                                <?php
                                if (!empty($prevUserPrize)) {
                                ?>
                                <label class="btn-pink-m" style="width: 160px;"><a href="/lotteryh5/cash_prize?user_id=<?= $userId?>&session_id=<?= $sessionId?>&user_prize_id=<?= $prevUserPrize->id ?>&lottery_id=<?= $lotteryId ?>&story_id=<?= $storyId ?>">上一个</a></label>
                                <?php
                                }

                                if (!empty($nextUserPrize)) {
                                ?>
                                    <label class="btn-pink-m" style="width: 160px;"><a href="/lotteryh5/cash_prize?user_id=<?= $userId?>&session_id=<?= $sessionId?>&user_prize_id=<?= $nextUserPrize->id ?>&lottery_id=<?= $lotteryId ?>&story_id=<?= $storyId ?>">下一个</a></label>
                                <?php
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