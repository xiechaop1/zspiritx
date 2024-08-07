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
 * @var \common\models\QA $qa
 */

\frontend\assets\Marginh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '消息';

?>
<div class="w-100 m-auto">
    <!--组队倒计时时间-->
    <input type="hidden" name="countdown" value="10">

    <div class="p-20 bg-black">
        <div class="match-circle m-t-50">
            <div class="match-circle1">
                <div class="match-circle2">
                    <div class="match-circle3">
                        <div class="match-circle4">
                            <img src="<?= $rivalAvatar ?>" class="header-m">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="match-text-box m-t-50">
            <div class="match-title1 start-race">
                匹配成功
            </div>

            <div class="match-title1" style="color:#4DAF51; margin-left: 50px; margin-right: 50px; text-align: left; margin-top: 15px; line-height: 120%;font-size:24px;">
                规则：<br><?php
                if ($matchType == \common\models\StoryMatch::MATCH_TYPE_CHALLENGE) {
                ?>
                你和对手同时做题，谁先回答正确，会对对方造成伤害。<br>
                最先血量为0的一方，输掉比赛。<br>
                获胜方有金币奖励。
                <?php
                } else if ($matchType == \common\models\StoryMatch::MATCH_TYPE_CONTEST) {
                ?>
                你和对手在规定时间内，谁完成的题目数越多，谁获胜。<br>
                获胜方有金币奖励。
                <?php
                }
                ?>
            </div>

            <div class="match-title1  start-race-disable hide">
                匹配中(<span class="match-title-tag-1">1</span>/<span class="match-title-tag-2">30</span>)
            </div>
            <div class="match-text1 m-t-20 start-race-disable hide">
                倒计时：<span class="match-text-tag-1">115</span>s
            </div>
        </div>

        <div class="text-center m-t-80 m-b-20">
            <label class="btn-green-m start-race-disable hide" >开始比赛</label>
            <span id="start_btn" href="/matchh5/challenge?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>&match_id=<?= $matchId ?>&qa_id=<?= $qaId ?>">
                <label class="btn-green-m active  start-race">开始比赛</label>
            </span>

        </div>

    </div>
</div>



<div class="w-100 m-auto" style="display: none; top: 20px;">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        消息
                    </div>
                    <?= $msg ?>
                </div>
            </div>
            <div class="btn-m-green m-t-30 float-right m-r-20">
                <span id="start_btn1" href="/matchh5/challenge?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>&match_id=<?= $matchId ?>&qa_id=<?= $qaId ?>">
                开始战斗
                </span>
            </div>
        </div>
        </div>

    </div>
<div class="row modal" id="message-box" style="top: 150px; z-index: 999999999;">
    <div class="m-t-30 col-sm-12 col-md-12 p-40">
        <!--                    <img src="../../static/img/match/bc_win.png" alt="" class="img-responsive  d-block m-auto"/>-->
        <div style="clear:both; margin: 10px; padding: 30px; border: 2px solid yellow; text-align: left; background-color: rgba(0,0,0, 0.5)">

            <input type="hidden" name="message_id" id="message-id">
            <input type="hidden" name="message_topic" id="message-topic">
            <span style="float: left; width: 100px;">
                        <img src="../../static/img/match/sugg.png" style="width: 80px;" alt="" class="img-responsive  d-block m-auto"/>
                        </span>
            <span class="fs-50 bold" style="color: #ffa227;">提&nbsp;示</span>
            <div style="clear: both; border-top: 1px solid #ffa227; padding-top: 15px; ">
                        <span class="answer-detail" id="message-content" style="line-height: 45px; color: yellow">

                        </span>
            </div>
        </div>
        <br>
        <!--                    <div class="answer-title m-t-40">-->
        <!--                        恭喜您，挑战成功！-->
        <!--                    </div>-->
<!--        <div class="btn-m-green m-t-30  m-l-30 msg-rtn-btn">-->
<!--            继续-->
<!--        </div>-->
        <!--                    <div class="answer-detail m-t-40" style="line-height: 40px;">-->
        <!--                        --><?php //echo ($qa['st_answer'] != 'True' && $qa['st_answer'] != $qa['st_selected']) ? $qa['st_answer'] : ''; ?>
        <!--                    </div>-->
    </div>

</div>

</div>
<script>
    window.onload = function () {
        var url = $('#start_btn').attr('href');
        $('#start_btn').click(function () {
            $('#message-content').html('正在生成题目，请稍等...');
            $('#message-box').modal('show');
            location.href = url;
        });
    };
</script>
