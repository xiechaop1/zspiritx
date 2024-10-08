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

\frontend\assets\Matchh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '故事汇';

?>
<style>
    .answer-tag-word {
        position: relative;
        margin-left: 80px;
    }
    .code-input {
        display: flex;
    }

    .play_voice_btn_disable {
        filter: grayscale(100%);
    }

    .code-input input {
        width: 55px;
        height: 75px;
        margin: 0 10px;
        text-align: center;
        font-size: 50px;
        color: yellow;
        border: 2px solid white;
        border-radius: 14px;
        transition: border-color 0.3s;
    }

    .code-input input:focus {
        border-color: #0c84ff;
        color: yellow;
        outline: none;
        background-color: #0b3452;
    }

    .code-input input[type=button] {
        width: 100px;
        height: 75px;
        margin: 0 10px;
        position: absolute;
        right: 10px;
        background-color: #0b3452;
        text-align: center;
        font-size: 50px;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 24px;
        transition: border-color 0.3s;
    }

    .keyboard_area .keyboard {
        width: 100px;
        height: 75px;
        margin: 0 10px;
        background-color: #0b3452;
        text-align: center;
        font-size: 50px;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 24px;
        transition: border-color 0.3s;
    }

    .keyboard_area .v_keyboard {
        width: 100px;
        height: 75px;
        margin: 0 10px;
        background-color: #0b3452;
        text-align: center;
        font-size: 50px;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 24px;
        transition: border-color 0.3s;
    }

    .keyboard_area .DELETE {
        background-color: #a83800;
        border: 2px solid #a80057;
    }

    .keyboard_area .keyboard_click {
        background-color: #0c84ff;
    }

    .answer-border-response {
        height: 75px;
        margin: 0 10px;
        text-align: center;
        color: yellow;
        border: 2px solid white;
        border-radius: 14px;
        transition: border-color 0.3s;
        font-size: 24px;
    }

    .keyboard_area .v_div_keyboard {
        float: left;
        width: 120px;
        height: 120px;
        margin: 0 10px;
        background-color: #0b3452;
        text-align: center;
        font-size: 50px;
        color: white;
        border: 2px solid #0c84ff;
        border-radius: 24px;
        transition: border-color 0.3s;
    }

    .keyboard_area .keyboard_label_big {
        clear: both;
        font-size: 40px;
        font-weight: bold;
        color: white;
        margin: 0px;
        padding: 0px;
    }

    .keyboard_area .keyboard_label_small {
        clear: both;
        font-size: 24px;
        color: white;
        margin: 0px;
        padding: 0px;
    }
    .keyboard_area .keyboard_label_delete {
        clear: both;
        font-size: 40px;
        color: red;
        margin: 0px;
        padding: 0px;
    }
</style>
<audio autoplay loop>
    <source src="" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="begin_ts" value="<?= time() ?>">
<input type="hidden" name="qa_type" id="qa_type" value="<?= $qa['qa_type'] ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="match_class" value="<?= !empty($matchClass) ? $matchClass : 0 ?>">
<input type="hidden" name="rtn_answer_type" id="rtn_answer_type" value="<?= $rtnAnswerType ?>">
<input type="hidden" name="level" value="<?= $level ?>">

<input type="hidden" name="subj_idx" id="subj_idx" value="0">
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
        <div class="p-20 bg-black">
            <div class="m-t-20">
                <div class="match-qa-header-left3" style="width: 435px; text-align: left;">
                    <img src="<?= $user['avatar'] ?>" class="header-l">
                    <div class="progress-title">
                        <span class="text-1 text-FF"><?= $user->user_name ?></span>
                        <img src="../../static/img/match/coin.png" class="m-l-20 m-r-10">
                        <span id="gold"><?= !empty($userScore->score) ? $userScore->score : 0 ?></span>
                    </div>
                </div>
                <div class="btn-m-green confirm_btn" style="margin-left: 80px;">
                    返回
                </div>

            </div>

            <div class="match-qa-box right">
                <!--文本问题-->
                <div class="match-qa-content-text" style="line-height: 125%;" id="topic">
<!--                    ︎开并百花丛，独立疏篱趣未穷。-->
                </div>
                <!--图片问题-->
                <div class="match-qa-content-img" style="display: none;" id="image">
                    <img src="../../static/img/example.png" class="img-w-100">
                </div>
                <div class="match-qa-content-worry hide">
                    <img src="../../static/img/match/worry.png">
                    <span>17</span>
                </div>
                <div class="match-qa-content-right hide">
                    <img src="../../static/img/match/right.png">
                    <span>17</span>
                </div>
                <div class="d-block text-center m-t-50" style="margin-top: 10px;">
                    <div class="match-info" style="margin: 10px auto;" data-toggle="modal" data-target="#extend-info">
                        <img src="../../static/img/match/Frame.png" class="img-coin">
                        详情
                    </div>
                    <div class="match-info sugg-btn" style="margin: 10px auto;" data-toggle="modal" data-target="#challenge-info">
                        <img src="../../static/img/match/Frame.png" class="img-coin">
                        提示
                    </div>
                </div>

                <div class="match-clock-bottom">
                    <div class="match-clock-bottom-left">
                        答题进度
                            ：<span class="text-1" id="subjct">0</span>
                        &nbsp; 难度
                        <span class="text-2" ><?= $level ?></span>
                    </div>
                    <div class="match-clock-bottom-right">
                        正确
                        <span class="text-1" id="right_ct">0</span>/错误
                        <span class="text-2" id="wrong_ct">0</span>
                    </div>

                </div>
            </div>

            <div class="m-t-100" style="margin-top: 75px;" id="answer-box">
<!--                <div class="answer-border2 worry">-->
<!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_1">-->
<!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_1">-->
<!--                        15-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="answer-border2 right">-->
<!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_2">-->
<!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_2">-->
<!--                        15-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="answer-border2">-->
<!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_3">-->
<!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_3">-->
<!--                        15-->
<!--                    </label>-->
<!--                </div>-->
<!--                <div class="answer-border2">-->
<!--                    <input class="form-check-input" type="checkbox" name="challenge_answer" value="1" id="legal_person_yes_4">-->
<!--                    <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_4">-->
<!--                        15-->
<!--                    </label>-->
<!--                </div>-->
            </div>


        </div>
    </div>


</div>
<div class="row hide">


    <label id="answer-info" class="h5-btn-green-big answer-btn hide"  data-value="<?php echo $qa['st_selected']; ?>
" data-qa="<?php echo $qa['id']; ?>" data-type="<?php echo $qa['qa_type']; ?>" data-story="<?php echo $qa['story_id']; ?>" data-user="">
        提交
    </label>
</div>
<div class="row modal fade" id="answer-right-box" style="top: 100px;">
    <div class="m-t-30 col-sm-12 col-md-12 p-40">
        <!--                    <img src="../../static/img/qa/Frame@2x.png" alt="" class="img-responsive  d-block m-auto"/>-->
        <img src="../../static/img/match/bc_win.png" alt="" class="img-responsive  d-block m-auto"/>
        <div style="clear:both; text-align: center;">
                        <span>
                            <!-- ../../static/img/qa/gold.gif -->
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 125px; height: 125px;" class=""/>
                            </span>

            <span class="answer-detail" id="gold_score" style="color: yellow">

                        </span>
        </div>
        <br>
        <!--                    <div class="answer-title m-t-40">-->
        <!--                        恭喜您，挑战成功！-->
        <!--                    </div>-->
        <div class="btn-m-green m-t-30  m-l-30 confirm_btn">
            继续
        </div>

        <!--                    <div class="btn-m-green m-t-30  m-l-30 msg-rtn-btn">-->
        <!--                        继续-->
        <!--                    </div>-->
        <!--                    <div class="answer-detail m-t-40" style="line-height: 40px;">-->
        <!--                        --><?php //echo ($qa['st_answer'] != 'True' && $qa['st_answer'] != $qa['st_selected']) ? $qa['st_answer'] : ''; ?>
        <!--                    </div>-->
    </div>

</div>
<div class="row modal fade" id="answer-error-box" style="top: 220px;">
    <div class="m-t-60 col-sm-12 col-md-12">
        <div class="answer-detail " >
            <!--                        <img src="../../static/img/qa/icon_错误提示@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
            <img src="../../static/img/match/bc_lose.png" alt="" class="img-responsive  d-block m-auto"/>
            <br>
            <!--                        <span  class=" d-inline-block vertical-mid">很遗憾，挑战失败！</span>-->
            <div class="btn-m-green m-t-30  m-l-30 retry_btn">
                再试一次
            </div>
        </div>
    </div>
</div>

<!-- 按钮：提示信息 -->
<div class="modal fade" id="challenge-info" tabindex="-1" style="display: none;" aria-hidden="true">
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
                    <div class="fs-36  text-FF  text-center bold lottery-error-title">
                        提示
                    </div>

                    <input type="hidden" id="message-topic">
                    <input type="hidden" id="message-id">
                    <div id="message-content" class="fs-40 text-FF text-center bold m-t-50 lottery-content" style="height: 600px; overflow: auto;">
                        <div id="message-content-ai" class="bold fs-28 message-content-ai" style="font-size: 28px; width: 80%;"></div>
                        <div style="float: left;"><img src="/template/img/qa/btn_播放_nor@2x.png"></div>
                    </div>
                    <div id="message-question" style="height: 200px; overflow: auto;">
<!--                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-20" data-dismiss="modal" style="margin-top: 25px;">-->
<!--                        <label class="btn-green-m-choice active ">知道了</label>-->
<!--                    </div>-->
                    </div>
<!--                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">-->
<!--                        <label class="btn-green-m active ">知道了</label>-->
<!--                    </div>-->
<!--                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">-->
<!--                        <label class="btn-green-m active ">知道了</label>-->
<!--                    </div>-->
<!--                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">-->
<!--                        <label class="btn-green-m active ">知道了</label>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="extend-info" tabindex="-1" style="display: none;" aria-hidden="true">
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
                    <div class="fs-36  text-FF  text-center bold lottery-error-title">
                        详情
                    </div>

                    <div id="extend-content-container" class="fs-32 text-FF bold m-t-50 lottery-content" style="height: 600px; overflow: auto;">
                        <div id="extend-content"></div>
                        <div style="float: left; line-height: 200%;">
                            <img class="play_voice_extend" src="../../static/img/match/play.png" width="50">
                        </div>
                    </div>

                                        <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">
                                            <label class="btn-green-m active ">知道了</label>
                                        </div>
                    <!--                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">-->
                    <!--                        <label class="btn-green-m active ">知道了</label>-->
                    <!--                    </div>-->
                    <!--                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">-->
                    <!--                        <label class="btn-green-m active ">知道了</label>-->
                    <!--                    </div>-->
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
                        恭喜您，挑战成功
                    </div>
                    <div class="text-center m-t-30">
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
                        很遗憾，挑战失败
                    </div>
                    <div class="m-t-40 bg-F5 p-20 fs-26 text-orange border-radius-r-5 border-radius-l-5">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    var obj;
    var userWareIds;
    var max = 0;
    var myPropObj;
    var rivalTimerObj;
    var rivalTimerRunning;
    var topicSuggestion;
    window.onload = function () {
        var i = 0;


        $('.msg-rtn-btn').click(function() {
            $('#message-box').modal('hide');
            // startRivalTimer($('#match_type').val());
        });

        $('.sugg-btn').click(function() {
            // $('#message-box').modal('show');
            // $('#message-box').modal('show');

            if ($('#message-topic').val() == $('#topic').html()) {
                return;
            }
            $('#message-content').html('正在思考……');

            setTimeout(function () {
                getSugg('');
            }, 500);
             $("#message-box").modal('show');

        });

        var audio_list = [];
        $('.play_voice_extend').click(function() {
            var thisObj = $(this);
            if (thisObj.hasClass('play_voice_btn_disable')) {
                return;
            }

            var user_id = $('input[name=user_id]').val();
            var story_id = $('input[name=story_id]').val();
            var audioVoice = $('#audio_voice')[0];

            thisObj.addClass('play_voice_btn_disable');
            for (var ai in audio_list) {
                if (audio_list[ai].msg == $(this).parent().prev().html()) {
                    audioVoice.src = audio_list[ai].voice;
                    audioVoice.play();
                    return;
                }
            }

            var msg = $(this).parent().prev().html();
            var userId = $('input[name=user_id]').val();
            console.log(msg);

            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: true,
                url: '/match/play_voice',
                data: {
                    story_id: story_id,
                    user_id: user_id,
                    messages: msg,
                },
                onload: function (data) {
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    thisObj.removeClass('play_voice_btn_disable');
                    console.log("ajax请求失败:" + XMLHttpRequest, textStatus, errorThrown);
                    $.alert("网络异常，请检查网络情况");
                },
                success: function (data, status) {
                    var dataContent = data;
                    var dataCon = $.toJSON(dataContent);
                    var voiceObj = eval("(" + dataCon + ")");//转换后的JSON对象

                    console.log(voiceObj);
                    audioVoice.src = voiceObj.data.file.file;

                    audio_list.push({
                        msg: msg,
                        voice: voiceObj.data.file.file,
                    });

                    audioVoice.play();
                    thisObj.removeClass('play_voice_btn_disable');

                }
            });
        });

        console.log(obj);
        // generateSubjects();

    };


    function getSugg(ques) {
        // $('#suggestion_content').toggle();
        var topic = $('#topic').html();
        var level = $('input[name=level]').val();
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();
        // var ques = '';
        var old_messages = '';

        var audio_list = [];

        if (ques != '') {
            var oldMsgs = [];
            var tmpMessages = $('#message-content').find('div');
            tmpMessages.each(function () {
                console.log($(this));
                var msg = $(this).html();
                console.log(msg);
                var msg_type = $(this).attr('msg_type');
                if (msg_type != "btn") {
                    var msgObj = {
                        msg: msg,
                        msg_type: msg_type,
                    };
                    console.log(msgObj);
                    oldMsgs.push(msgObj);
                }
            });
            console.log(oldMsgs);
            old_messages = JSON.stringify(oldMsgs);
        } else {
            $('#message-content').html('');
        }
        console.log(ques);
        $('#message-question').html('');
        var sugdiv = '<div class="fs-24 btn-green-m-msg-ai-choice active message-content-ai" msg_type="assistant" style="float: left; clear:both; font-size: 24px; width: 80%;">正在思考……</div>';
        $('#message-content').append(sugdiv);

        var audioVoice = $('#audio_voice')[0];

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: true,
            url: '/match/get_suggestion_from_subject',
            data:{
                story_id:story_id,
                user_id:user_id,
                topic:topic,
                ques:ques,
                old_messages:old_messages,
                level:level,
                match_class:match_class,
            },
            onload: function (data) {
                $('#answer-border-response').html('处理中……');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var ajaxObj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //console.log("ajax请求成功:"+data.toString())

                //新消息获取成功
                if(ajaxObj["code"]==200){
                    console.log(ajaxObj);
                    var suggestion = ajaxObj.data.suggestion;
                    if ($('#message-content').html() == '正在思考……')  {
                        $('#message-content').html('');
                    }
                    // var sugdiv = '<div class="fs-24 btn-green-m-msg-ai-choice active message-content-ai" msg_type="assistant" style="clear:both; font-size: 24px; width: 80%;">' + suggestion + '</div>';
                    // $('#message-content').append(sugdiv);
                    // $('#message-content').html(suggestion);
                    $('.message-content-ai').last().html(suggestion);
                    $('#message-content').append('<div style="float: left; line-height: 200%;" msg_type="btn"><img class="play_voice" src="../../static/img/match/play.png" width="50"></div>');
                    // var audioVoice = $('#audio_voice')[0];
                    // audioVoice.src = ajaxObj.data.voice;
                    // audioVoice.play();
                    $('#message-topic').val(topic);
                    var msbox = document.querySelector('#message-content');
                    msbox.scrollTo(0, msbox.scrollHeight - msbox.clientHeight);
                    console.log(ajaxObj.data.questions);
                    $('#message-question').html('');
                    for (var qu in ajaxObj.data.questions) {
                        var qutitle = ajaxObj.data.questions[qu];
                        var size = 28;
                        if (qutitle.length > 20) {
                            size = 20;
                        }
                        var ques = '<div class="fs-36 text-F6 text-center bold m-t-50 m-b-20 next-ques" style="margin-top: 25px;">';
                        ques += '<label class="btn-green-m-choice active " style="font-size: ' + size + 'px;">' + ajaxObj.data.questions[qu]  + '</label>';
                        ques += '</div>';
                        console.log(ques);
                        $('#message-question').append(ques);
                    }
                    $('.next-ques').click(function() {
                        var next_ques = $(this).find('label').html();
                        // console.log(next_ques);
                        var msgdiv = '<div class="fs-24 btn-green-m-msg-ai-choice old-msg-content my message-content-ai" msg_type="user" style="clear:both; font-size: 24px; width: 80%; float: right">' + next_ques + '</div>';
                        $('#message-content').append(msgdiv);
                        var msbox = document.querySelector('#message-content');
                        msbox.scrollTo(0, msbox.scrollHeight - msbox.clientHeight);
                        getSugg(next_ques);
                    });

                    $('.play_voice').click(function() {
                        var thisObj = $(this);
                        if (thisObj.hasClass('play_voice_btn_disable')) {
                            return;
                        }
                        thisObj.addClass('play_voice_btn_disable');
                        for (var ai in audio_list) {
                            if (audio_list[ai].msg == $(this).parent().prev().html()) {
                                audioVoice.src = audio_list[ai].voice;
                                audioVoice.play();
                                return;
                            }
                        }

                        var msg = $(this).parent().prev().html();
                        var userId = $('input[name=user_id]').val();
                        console.log(msg);

                        $.ajax({
                            type: "GET", //用POST方式传输
                            dataType: "json", //数据格式:JSON
                            async: true,
                            url: '/match/play_voice',
                            data: {
                                story_id: story_id,
                                user_id: user_id,
                                messages: msg,
                            },
                            onload: function (data) {
                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                thisObj.removeClass('play_voice_btn_disable');
                                console.log("ajax请求失败:" + XMLHttpRequest, textStatus, errorThrown);
                                $.alert("网络异常，请检查网络情况");
                            },
                            success: function (data, status) {
                                var dataContent = data;
                                var dataCon = $.toJSON(dataContent);
                                var voiceObj = eval("(" + dataCon + ")");//转换后的JSON对象

                                console.log(voiceObj);
                                audioVoice.src = voiceObj.data.file.file;

                                audio_list.push({
                                    msg: msg,
                                    voice: voiceObj.data.file.file,
                                });

                                audioVoice.play();
                                thisObj.removeClass('play_voice_btn_disable');

                            }
                        });
                    });

                    // $('#message-box').modal('show');
                }
                //新消息获取失败
                else{
                    $.alert(ajaxObj.msg)
                }

            }
        });
    }


</script>
