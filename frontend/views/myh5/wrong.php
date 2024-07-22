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

$this->title = '错题本';

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
    <source src="" type="audio/mpeg">
    您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="begin_ts" value="<?= time() ?>">
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
                        <span id="gold">0</span>
                    </div>
                    <input type="hidden" class="show_max_hp" id="<?= $user->id ?>" value="<?= !empty($myProp['hp']) ? $myProp['hp'] : 300  ?>">
<!--                    <div class="progress w-100">-->
<!--                        <div id="my_hp" class="progress-bar" role="progressbar" aria-valuenow="--><?php //= !empty($myProp['hp']) ? $myProp['hp'] : 300 ?><!--"-->
<!--                             aria-valuemin="0" aria-valuemax="100" style="width: 100%;">-->
<!--                            <span class="sr-only">40% 完成</span>-->
<!--                        </div>-->
<!--                    </div>-->
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
                    <div class="match-info sugg_btn" style="margin: 10px auto;" data-toggle="modal" data-target="#challenge-info">
                        <img src="../../static/img/match/Frame.png" class="img-coin">
                        提示
                    </div>
                    <div class="match-info" id="prev" style="margin: 10px auto;">
                        上一题
                    </div>
                    <div class="match-info" id="next" style="margin: 10px auto;">
                        下一题
                    </div>
                </div>

                <div class="match-clock-bottom">
                    <div class="match-clock-bottom-left">
                        进度

                        <span class="text-1" id="subjct">0</span> / <span class="text-2" ><?= $ct ?></span>
                    </div>


                </div>
            </div>

            <div class="m-t-100" style="margin-top: 50px;" id="answer-box">
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
                    <div id="message-content" class="fs-40 text-FF text-center bold m-t-50 lottery-content" style="font-size: 35px; line-height: 150%;">

                    </div>
                    <div class="fs-36 text-F6 text-center bold m-t-50 m-b-40" data-dismiss="modal">
                        <label class="btn-green-m active ">知道了</label>
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
    var max = <?= $ct ?>;
    var myPropObj;
    var rivalTimerObj;
    var rivalTimerRunning;
    var topicSuggestion;
    window.onload = function () {
        var i = 0;
        var max = <?= $ct ?>;
        var match_type = $('#match_type').val();


        if (match_type == 3) {
            var matchTimer = setInterval(function() {
                // $('#msg_' + i).show();
                // if ($('#msg_' + i).length > 0) {
                //     $('#msg_' + i).get(0).scrollIntoView();
                // }
                compTimer(matchTimer);
                // console.log(i);
                i++;
            }, 1000);
        }

// showSubject(0, obj);
        var dataContent = <?= $subjectsJson ?>;
        var dataCon=$.toJSON(dataContent);
        obj = eval( "(" + dataCon + ")" );

        max = <?= $ct ?>;

        showSubject(0);

        $('.msg-rtn-btn').click(function() {
            $('#message-box').modal('hide');
            // startRivalTimer($('#match_type').val());
        });

        $('.sugg_btn').click(function() {
            // $('#message-box').modal('show');
            // $('#message-box').modal('show');

            if ($('#message-topic').val() == $('#topic').html()) {
                return;
            }
            $('#message-content').html('正在思考……');

            setTimeout(function () {
                getSugg();
            }, 500);
             $("#message-box").modal('show');

        });

        $('#prev').click(function() {
            var idx = $('#subj_idx').val();
            console.log(idx);
            idx = parseInt(idx);
            if (idx > 0) {
                idx--;
                showSubject(idx);
            }
        });

        $('#next').click(function() {
            var idx = $('#subj_idx').val();
            console.log(idx);
            idx = parseInt(idx);
            if (idx < max - 1) {
                idx++;
                showSubject(idx);
            }
        });

    };

    
    function getSugg() {
        // $('#suggestion_content').toggle();
        var topic = $('#topic').html();
        var level = $('input[name=level]').val();
        var match_class = $('input[name=match_class]').val();
        var user_id = $('input[name=user_id]').val();
        var story_id = $('input[name=story_id]').val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/get_suggestion_from_subject',
            data:{
                story_id:5,
                user_id:user_id,
                topic:topic,
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
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //console.log("ajax请求成功:"+data.toString())

                //新消息获取成功
                if(obj["code"]==200){
                    console.log(obj);
                    var suggestion = obj.data.suggestion;
                    var size = obj.data.size;
                    $('#message-content').html(suggestion);
                    if (size != undefined) {
                        $('#message-content').css('font-size', size);
                    }
                    $('#message-topic').val(topic);
                    // $('#message-box').modal('show');
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });
    }

    function showSubject(idx) {
        $('#subjct').html(idx+1);
        console.log(obj[idx]);
        var qa = obj[idx].qa;
        var user_qa = obj[idx].user_qa;
        var topic = qa.topic;
        var size = qa.size;
        console.log(topic);
        if (topic == undefined) {
            idx = 0;
            var topic = qa.topic;
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

        $('#subj_idx').val(idx);

        var qa_type = qa.qa_type;
        if (qa_type == 1 || qa_type == 30) {
            var ansrange = qa.selected_json;
            console.log(ansrange);
            var optHtml = '';
            for (var j = 0; j < ansrange.length; j++) {
                label = String.fromCharCode(j + 65);

                var border2_class = '';
                if (user_qa.answer == ansrange[j]) {
                    border2_class = 'worry';
                }
                if (qa.st_answer == ansrange[j]) {
                    border2_class = 'right';
                }

                optHtml += '<div class="answer-border2 ' + border2_class + '">';
                optHtml += '     <input class="form-check-input" type="radio" name="challenge_answer" value="' + ansrange[j] + '" id="legal_person_yes_' + label + '">';
                optHtml += '        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_' + label + '">';
                optHtml += ansrange[j];
                optHtml += '        </label>';
                optHtml += '</div>';

            }
        }
        // console.log(optHtml);
        $('#answer-box').html(optHtml);
        $('#suggestion_content').fadeOut();
    }

</script>
