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

$this->title = '练习赛';

?>
<style>
    .answer-tag-word {
        position: relative;
        margin-left: 80px;
    }
    .code-input {
        display: flex;
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
    <source src="<?= $qa['voice'] ?>" type="audio/mpeg">
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
                <div class="match-qa-header-left3" style="width: 415px; text-align: left;">
                    <img src="<?= $user['avatar'] ?>" class="header-l">
                    <div class="progress-title">
                        <span class="text-1 text-FF"><?= $user->user_name ?></span>
                        <img src="../../static/img/match/coin.png" class="m-l-20 m-r-10">
                        <span id="gold"><?= !empty($userScore->score) ? $userScore->score : 0 ?></span>
                    </div>
                </div>
                <div class="btn-m-green confirm_btn" style="margin-left: 100px;">
                    返回
                </div>

            </div>

            <div class="match-qa-box right">
                <!--文本问题-->
                <div class="match-qa-content-text" style="line-height: 125%;" id="topic">
<!--                    ︎开并百花丛，独立疏篱趣未穷。-->
                </div>
                <!--图片问题-->
                <div class="match-qa-content-img" style="display: none;">
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
                    <div class="match-info" style="margin: 10px auto;" data-toggle="modal" data-target="#challenge-info">
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
    var max = 0;
    var myPropObj;
    var rivalTimerObj;
    var rivalTimerRunning;
    var topicSuggestion;
    window.onload = function () {
        var i = 0;
        //var max = <?php //= $ct ?>//;
        // var match_type = $('#match_type').val();


        // if (match_type == 3) {
        //     var matchTimer = setInterval(function() {
        //         // $('#msg_' + i).show();
        //         // if ($('#msg_' + i).length > 0) {
        //         //     $('#msg_' + i).get(0).scrollIntoView();
        //         // }
        //         compTimer(matchTimer);
        //         // console.log(i);
        //         i++;
        //     }, 1000);
        // }

// showSubject(0, obj);
        var dataContent = <?= $subjectsJson ?>;
        var dataCon=$.toJSON(dataContent);
        obj = eval( "(" + dataCon + ")" );

        showSubject(0);

        $('.msg-rtn-btn').click(function() {
            $('#message-box').modal('hide');
            // startRivalTimer($('#match_type').val());
        });

        $('.match-info').click(function() {
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

        console.log(obj);
        generateSubjects();

    };

    function generateSubjects() {
        return true;
        var topic = $('#topic').html();
        var level = $('input[name=level]').val();
        var match_class = $('input[name=match_class]').val();
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: true,
            url: '/match/get_subjects',
            data:{
                story_id:story_id,
                user_id:user_id,
                // topic:topic,
                level:level,
                match_class:match_class,
                ct:5,
            },
            onload: function (data) {
                // $('#answer-border-response').html('处理中……');
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
                var subjidx = $('#subj_idx').val();
                console.log(subjidx);
                console.log(ajaxObj.data);
                if(ajaxObj["code"]==200){
                    var tmpObj = [];
                    for (var i = 0; i <= subjidx; i++) {
                        tmpObj.push(obj[i]);
                    }
                    for (var i = 0; i < ajaxObj.data.length; i++) {
                        tmpObj.push(ajaxObj.data[i]);
                    }
                    console.log(obj.length);
                    for (var i = parseInt(subjidx) + 1; i < obj.length; i++) {
                        tmpObj.push(obj[i]);
                    }
                    obj = tmpObj;
                    console.log(obj);
                }
                //新消息获取失败
                else{
                    $.alert(ajaxObj.msg)
                }

            }
        });
    }

    function getSugg(ques) {
        // $('#suggestion_content').toggle();
        var topic = $('#topic').html();
        var level = $('input[name=level]').val();
        var match_class = $('input[name=match_class]').val();
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();
        // var ques = '';
        var old_messages = '';

        if (ques != '') {
            var oldMsgs = [];
            var tmpMessages = $('#message-content').find('div');
            tmpMessages.each(function () {
                console.log($(this));
                var msg = $(this).html();
                console.log(msg);
                var msg_type = $(this).attr('msg_type');
                var msgObj = {
                    msg: msg,
                    msg_type: msg_type,
                };
                console.log(msgObj);
                oldMsgs.push(msgObj);
            });
            console.log(oldMsgs);
            old_messages = JSON.stringify(oldMsgs);
        } else {
            $('#message-content').html('');
        }
        console.log(ques);
        $('#message-question').html('');
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
                    var sugdiv = '<div class="fs-24 btn-green-m-msg-ai-choice active message-content-ai" msg_type="assistant" style="clear:both; font-size: 24px; width: 80%;">' + suggestion + '</div>';
                    // $('#message-content').html(suggestion);
                    $('#message-content').append(sugdiv);
                    $('#message-topic').val(topic);
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
                        var msgdiv = '<div class="fs-24 btn-green-m-msg-ai-choice my message-content-ai" msg_type="user" style="clear:both; font-size: 24px; width: 80%; float: right">' + next_ques + '</div>';
                        $('#message-content').append(msgdiv);
                        getSugg(next_ques);
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

    function showSubject(idx) {
        var topic = obj[idx].topic;
        var size = obj[idx].size;
        if (topic == undefined) {
            idx = 0;
            var topic = obj[idx].topic;
        }
        if (topic == undefined) {
            return;
        }
        if (topic.indexOf('http') >= 0) {
            $('#image').html('<img src="' + topic + '" alt="" class="img-responsive d-block"/>');
            topic = '';
        } else {
            $('#image').html('');
        }
        $('#topic').html(topic);

        if (size != undefined) {
            $('#topic').css('font-size', size + 'px');
        }

        console.log(idx);
        $('#subj_idx').val(idx);

        var qa_type = $('#qa_type').val();
        if (qa_type == 1 || qa_type == 30) {
            var ansrange = obj[idx].selected_json;
            var optHtml = '';
            for (var j = 0; j < ansrange.length; j++) {
                label = String.fromCharCode(j + 65);
                optHtml += '<div class="answer-border2">';
                optHtml += '     <input class="form-check-input" type="radio" name="challenge_answer" value="' + ansrange[j] + '" id="legal_person_yes_' + label + '">';
                optHtml += '        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_' + label + '">';
                optHtml += ansrange[j];
                optHtml += '        </label>';
                optHtml += '</div>';
                // label = String.fromCharCode(j + 65);
                // optHtml += '<div class="m-t-30 col-sm-6 col-md-6"><div class="answer-border">';
                // optHtml += '<input class="form-check-input" type="radio" name="answer_c" value="' + ansrange[j] + '" id="legal_person_yes_' + label + '" >';
                // optHtml += '<label class="form-check-label fs-30 answer-btn" for="legal_person_yes_' + label + '">';
                // optHtml += '    <span class="answer-tag">' + label + '</span>' + '<span class="answer-tag-word">' + ansrange[j] + '</span>';
                // optHtml += '</label> </div></div>';
            }
            optHtml += '<input type="hidden" id="add_gold" value="10">';
            optHtml += '<input type="hidden" id="add_right" value="1">';
            optHtml += '<input type="hidden" id="add_wrong" value="0">';
        }
        // console.log(optHtml);
        $('#answer-box').html(optHtml);
        $('input[name=challenge_answer]').click(function() {
            // $('input[name=challenge_answer]').attr('disabled', true);
            submitSubject(idx, $(this));
            // $('input[name=answer_c]').attr('disabled', false);

        });
        $('#suggestion_content').fadeOut();
    }

    function sleep(ms) {
        setTimeout(function (){

        },ms);
    }


    function submitSubject(idx, chosenObj) {
        var answer = obj[idx].st_answer;
        var that=$("#answer-info");
        // var match_type = $('#match_type').val();
        var qa_type=that.attr("data-type");
        // console.log(match_type);
        // console.log(qa_type);
        if (qa_type == 1 || qa_type == 30 || qa_type == 2 || qa_type == 3 || qa_type == 4) {
            var v_select = $("input[name='challenge_answer']:checked").val();
        } else if (qa_type == 7) {
            var v_select = $("input[name='answer_txt']").val();
        } else if (qa_type == 9) {
            var v_select1 = $("input[name='answer_txt']").val();
            var v_select2 = '';
            // var v_select2 = $("#answer-border-response").html();
            var v_select = v_select2 + v_select1;
        } else if (qa_type == 8) {
            var v_selects = $("input[name='answer_txt']");
            var v_select = '';
            for (var i = 0; i < v_selects.length; i++) {
                v_select += v_selects[i].value;
            }
        }
        var chosen = v_select;
        console.log(chosen);
        // return false;
        // var chosen = $(chosenObj).val();

        if (chosen == answer) {

            showRet(chosenObj, 1);
            setTimeout(function (){

                addGold();
                addRight();
                addWrong();
                addSubjCt();
                // if (match_type == 2) {
                //
                //     if (ret == 0) {
                //         // clearInterval(timer);
                //         var answer = 1;
                //         submitAnswer(answer);
                //         // $('#answer-right-box').modal('show');
                //
                //     }
                // }

                recordQa(obj[idx], chosen);
                var nIdx = idx + 1;
                if (nIdx > obj.length - 10) {
                    generateSubjects();
                }

                showSubject(nIdx);
                $('input[name=answer_c]').attr('disabled', false);
            },500);

        } else {
            $('#add_gold').val('0');
            $('#add_right').val('0');
            $('#add_wrong').val('1');
            showRet(chosenObj, 2);
            recordQa(obj[idx], chosen);
            setTimeout(function () {
                $('input[name=answer_c]').attr('disabled', false);
            }, 1000);
        }
    }

    function showRet(retObj, answer) {
        var retCss = 'right';
        console.log(answer);
        if (answer != 1) {
            retCss = 'worry';
        }
        $(retObj).parent().addClass(retCss);
    }

    function recordQa(subjectObj, chosen) {
        console.log(subjectObj);
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();
        var session_id=$("input[name='session_id']").val();
        // var session_stage_id=$("input[name='session_stage_id']").val();
        var begin_ts=$("input[name='begin_ts']").val();
        var qa_mode = 3;
        var qa_type = $('#qa_type').val();
        var match_class = $('input[name=match_class]').val();
        var st_answer = subjectObj.st_answer;
        var topic = subjectObj.topic;
        var selected = subjectObj.selected;
        var st_selected = selected;
        var score = subjectObj.gold;
        var level = subjectObj.level;
        var link_qa_id = subjectObj.link_qa_id;

        var subjct=$('#subjct').html();
        var right_ct=$('#right_ct').html();
        var wrong_ct=$('#wrong_ct').html();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/qa/add_user_answer',
            data:{
                user_id:user_id,
                answer:chosen,
                story_id:story_id,
                session_id:session_id,
                // session_stage_id:session_stage_id,
                begin_ts:begin_ts,
                st_answer:st_answer,
                topic:topic,
                selected:st_selected,
                score:score,
                qa_mode:qa_mode,
                qa_type:qa_type,
                match_class:match_class,
                st_selected:st_answer,
                level:level,
                link_qa_id:link_qa_id,
                subj_ct:subjct,
                right_ct:right_ct,
                wrong_ct:wrong_ct,
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

                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg)
                    console.log(ajaxObj.msg);
                }

            }
        });

    }

    function addGold() {
        var gold = $('#gold').html();
        var addGold = $('#add_gold').val();
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/user/add_user_score',
            data:{
                user_id:user_id,
                story_id:story_id,
                // session_id:session_id,
                // session_stage_id:session_stage_id,
                score:addGold,
            },
            onload: function (data) {
                // $('#answer-border-response').html('处理中……');
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

                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg)
                    console.log(ajaxObj.msg);
                }

            }
        });

        if (addGold > 0) {

            var goldSpan = $('#gold');
            var goldDiv = '<div id="showGold" style="z-index: 999999; position: absolute; top: 100px; left: ' + goldSpan.position().left + 'px; font-size: 70px; color: #e0a800; font-weight: bold; width: 300px;">+ ' + addGold + '</div>';
            $('#gold').parent().append(goldDiv);
            console.log(goldDiv);
            $('#showGold').animate({
                top: goldSpan.position().top - 10 + 'px',
                opacity: '0'
            }, 1200, function() {
                $('#showGold').remove();
            });

            floNumber(addGold);
            gold = parseInt(gold) + parseInt(addGold);
            $('#gold').html(gold);
            $('#gold').css('opacity', 0).animate({
                opacity: 1
            }, 1000);
        }
    }

    function addRight() {
        var right = $('#right_ct').html();
        var addRight = $('#add_right').val();
        if (addRight > 0) {
            floNumber(addRight);
            right = parseInt(right) + parseInt(addRight);
            $('#right_ct').html(right);
            $('#right_ct').css('opacity', 0).animate({
                opacity: 1
            }, 1000);
        }
    }

    function addWrong() {
        var wrong = $('#wrong_ct').html();
        var addWrong = $('#add_wrong').val();
        if (addWrong > 0) {
            floNumber(addWrong);
            wrong = parseInt(wrong) + parseInt(addWrong);
            $('#wrong_ct').html(wrong);
            $('#wrong_ct').css('opacity', 0).animate({
                opacity: 1
            }, 1000);
        }
    }

    function submitAnswer(answer) {
        var that=$("#answer-info");
        var qa_id=that.attr("data-qa");
        var qa_type=that.attr("data-type");
        var story_id=that.attr("data-story");
        var user_id=$("input[name='user_id']").val();
        var session_id=$("input[name='session_id']").val();
        var session_stage_id=$("input[name='session_stage_id']").val();
        var begin_ts=$("input[name='begin_ts']").val();
        var v_ture=that.attr("data-value");
        var v_detail=that.attr("data-detail");
        var match_id=that.attr("data-match");

        var score=$('#gold').html();
        var subjct=$('#subjct').html();
        var right_ct=$('#right_ct').html();
        var wrong_ct=$('#wrong_ct').html();

        // var answer;
        // console.log(subjct);
        // console.log(max_riv_subjct);
        // if (subjct > max_riv_subjct) {
        //     answer = 1;
        // } else {
        //     answer = 0;
        // }

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/update_match',
            data:{
                user_id:user_id,
                qa_id:qa_id,
                story_id:story_id,
                match_id:match_id,
                session_id:session_id,
                begin_ts:begin_ts,
                score:score,
                subjct:subjct,
                right_ct:right_ct,
                wrong_ct:wrong_ct,
                answer:answer,
                // riv_subjct:max_riv_subjct
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

                //audio 素材
                var audio_right=$("#audio_right")[0];
                var audio_wrong=$("#audio_wrong")[0];

                //新消息获取成功
                if(ajaxObj["code"]==200){

                    if(answer == 1){
                        $("#answer-box").hide();
                        $("#answer-right-box").modal('show');
                        audio_right.play();

                        if (ajaxObj.data.score.score != undefined) {
                            var score_text = "+" + ajaxObj.data.score.score + "枚";
                            if (ajaxObj.data.score.addition > 0) {
                                score_text = score_text + "（奖：" + ajaxObj.data.score.addition + "枚）";
                            }
                            $("#gold_score").html(score_text);
                        }
                        $('#rtn_answer_type').val(1);   // 成功

                        // setTimeout(function (){
                        //     // Unity.call('WebViewOff&TrueAnswer');
                        //     var params = {
                        //         'WebViewOff':1,
                        //         'AnswerType':1
                        //     }
                        //     var data=$.toJSON(params);
                        //     Unity.call(data);
                        // },2000);
                    }
                    else{
                        $("#answer-box").hide();
                        // $("#answer-error-box").removeClass('hide');
                        $("#answer-error-box").modal('show');
                        $('#rtn_answer_type').val(2);   // 失败
                        // $("#h5-worry").modal('show');
                        audio_wrong.play();
                        // $(".retry_btn").show();
                        // setTimeout(function (){
                        //     // Unity.call('WebViewOff&FalseAnswer');
                        //     // var params = {
                        //     //     'WebViewOff':1,
                        //     //     'AnswerType':2
                        //     // }
                        //     // var data=$.toJSON(params);
                        //     // Unity.call(data);
                        //     location.reload();
                        // },2000);
                    }
                }
                //新消息获取失败
                else{
                    $.alert(ajaxObj.msg)
                }

            }
        });

    };

    function addSubjCt() {
        var subjct = $('#subjct').html();
        subjct++;
        $('#subjct').html(subjct);
    }

    function floNumber(num) {
        var duration = 5;
        var height = 0;
        $('#number-floater').html(num);
        $('#number-floater').css('opacity', 0) // 设置初始透明度为0
            .animate({
                top: '-=70',
                // 'font-size': 70,
                opacity: 1 // 渐显
            }, 200)
            .delay(duration) // 延迟随机时间
            .animate({
                top: '-=70',
                opacity: 0, // 渐隐
                'font-size': '-=15'
            }, 150, function() {
                $(this).css('top', height); // 动画完成后重置位置
                $(this).css('font-size', 40);
            });
    }

</script>
