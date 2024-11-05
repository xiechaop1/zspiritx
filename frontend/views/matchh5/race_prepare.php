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
<style>
    .avatar {
        margin: 15px 5px 5px 5px;
        float: left;
        width: 120px;
    }
</style>
<input type="hidden" name="match_id" value="<?= $matchId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="join_expire_time" value="<?= !empty($storyMatch->join_expire_time) ? $storyMatch->join_expire_time : 0 ?>">

<div class="w-100 m-auto">
    <!--组队倒计时时间-->
    <input type="hidden" name="countdown" value="10">
<!--    <div class="m-t-20">-->
<!--        <div class="match-qa-header-left3" style="width: 385px; height: 60px; text-align: center; padding: 0px 20px 0px 20px;">-->
<!--            <div class="progress-title">-->
<!--                <span class="text-1 text-FF">--><?php //= $userInfo->user_name ?><!--</span>-->
<!--                <img src="../../static/img/match/coin.png" class="m-l-20 m-r-10">-->
<!--                <span id="gold">--><?php //= !empty($userScore->score) ? \common\helpers\Common::formatNumberToStr($userScore->score, true, 0, 0) : 0 ?><!--</span>-->
<!--            </div>-->
<!--        </div>-->
<!---->
<!--    </div>-->

    <div class="p-20 bg-black">
        <div class="match-circle m-t-50">
            <div class="match-circle1">
                <div class="match-circle2">
                    <div class="match-circle3">
                        <div class="match-circle4">
                            <img src="<?= $avatar ?>" class="header-m">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="match-text-box m-t-80" style="margin-top: 20px;">
            <div class="match-title1 start-race hide">
                匹配成功
            </div>

            <div class="match-title1 race-prepare">
                准备匹配
            </div>

            <div class="match-title1" style="color:#4DAF51; margin-left: 50px; margin-right: 50px; text-align: left; margin-top: 15px; line-height: 120%;font-size:24px;">
                规则：<br>
                匹配2名玩家一起进行比赛<br>
                做对题可以进行下一道<br>
                谁先做完谁赢得比赛<br>
                赢取比赛有金币奖励
            </div>

            <div class="match-title1 race-already hide">
                已经开始
            </div>

            <div class="match-title1 race-matching start-race-disable hide">
                匹配中(<span class="match-title-tag-1" id="matching_player_ct">1</span>/<span class="match-title-tag-2"><?= $storyMatch->max_players_ct ?></span>)
            </div>
            <div class="match-text1 m-t-20 race-matching start-race-disable hide">
                倒计时：<span class="match-text-tag-1" id="timer"><?= !empty($storyMatch->join_expire_time) && $storyMatch->join_expire_time > time() ? $storyMatch->join_expire_time - time() : 0 ?></span>s
            </div>
        </div>

        <div class="text-center m-t-200 m-b-20" style="margin-top: 30px;">
            <label class="btn-green-m active hide matching-race">开始匹配</label>
            <label class="btn-green-m start-race-disable start-btn" >开始比赛</label>
            <a href="javascript:location.href=location.href;">
                <label class="btn-green-m active start-race-already hide" >重新比赛</label>
            </a>
            <a href="/matchh5/race?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>&match_id=<?= $matchId ?>&qa_id=<?= $qaId ?>">
                <label class="btn-green-m active hide start-race">开始比赛</label>
            </a>

        </div>

    </div>
</div>



<div class="w-100 m-auto" style="top: 20px; display: none;">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        匹配中
<!--                        (<span id="matching_player_ct"></span>/<span>--><?php //= $storyMatch->max_players_ct ?><!--</span>)-->
                    </div>
                    <div id="players">
                        <div style="float: left; width: 50px;"></div>
                    </div>

                </div>
            </div>
            <div class="m-t-30 bold btn-m-green" id="timer">

            </div>
            <div id="match_btn" class="btn-m-green m-t-30 float-right m-r-20" style="display: none;">
                <a href="/matchh5/race?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>&match_id=<?= $matchId ?>&qa_id=<?= $qaId ?>">
                    开始比赛
                </a>
            </div>
        </div>
    </div>

</div>

</div>
<script>
    window.onload = function(){
        var intervalObjs = new Array();
        var matching = setInterval(function(){
            getKnockoutStatus(intervalObjs);
            // clearInterval(matching);
        }, 1000);
        intervalObjs.push(matching);
        var timer = setInterval(function(){
            computeTimer(intervalObjs);
        }, 1000);
        intervalObjs.push(timer);

        $('.matching-race').click(function(){
            comMatch();
        });

        // setTimeout(function(){
            genSubjects();
        // }, 500);
    };

    function comMatch() {
        var match_id = $('input[name="match_id"]').val();
        var story_id = $('input[name="story_id"]').val();
        var user_id = $('input[name="user_id"]').val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/add_knock_player',
            data:{
                story_id:story_id,
                match_id:match_id,
                user_id:user_id,
                // session_id:session_id,
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
                if(obj["code"] == 200){

                } else{
                    $.alert(obj.msg)
                }

            }
        });
    }

    function genSubjects() {
        var match_id = $('input[name="match_id"]').val();
        var story_id = $('input[name="story_id"]').val();
        var user_id = $('input[name="user_id"]').val();

        console.log('Generating subjects ... ');
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/generate_subjects_to_knockout',
            data:{
                story_id:story_id,
                story_match_id:match_id,
                user_id:user_id,
                level_range:2,
                ct:10
                // session_id:session_id,
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
                if(obj["code"] == 200){

                } else{
                    // $.alert(obj.msg)
                }

            }
        });
    }

    function computeTimer(intervalObjs) {
        // 获取当前时间
        var nowTime = new Date();
        var timeInt = parseInt(nowTime.getTime()/1000);
        var cha = $('input[name="join_expire_time"]').val() - timeInt;

        // console.log(cha)

        if (cha > 0) {
            $('#timer').html(cha);
        } else {
            for (i in intervalObjs) {
                clearInterval(intervalObjs[i]);
            }
            // clearInterval(intervalObj);
            // $('#timer').fadeOut();
            // $('#match_btn').fadeIn();
            $('.start-race').removeClass('hide');
            $('.start-race-disable').hide();
        }
    }

    var playerIds = new Array();
    function getKnockoutStatus(intervalObjs) {
        var match_id = $('input[name="match_id"]').val();
        var story_id = $('input[name="story_id"]').val();
        var user_id = $('input[name="user_id"]').val();
        var hasMatching = 0;
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/get_knockout_status',
            data:{
                // user_id:user_id,
                story_id:story_id,
                match_id:match_id,
                user_id:user_id,
                match_type:5,
                // session_id:session_id,
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
                if(obj["code"] == 200){

                    // $('#players').html('');
                    $('#matching_player_ct').html(obj.data.players_ct);
                    var playerCt = 0;



                    if (obj.data.players.length > 0) {
                        for (i in obj.data.players) {
                            if (obj.data.players[i].id == user_id
                                && hasMatching == 0
                            ) {
                                // $('.race-matching').removeClass('hide');
                                $('.race-prepare').addClass('hide');
                                $('.start-race-disable').removeClass('hide');
                                hasMatching = 1;
                            }
                            // if (playerIds.indexOf(obj.data.players[i].id) > -1
                            //     || i >= 12
                            // ) {
                            //     continue;
                            // }
                            // var playerAvatar = obj.data.players[i].user.avatar;
                            // console.log(obj.data.players[i].id);
                            // playerIds.push(obj.data.players[i].id);
                            // var avatarDiv = '<div id="avatar_' + obj.data.players[i].user.id + '" class="d-flex m-b-10 avatar"><img src="' + playerAvatar + '" width="100"></div>';
                            // $('#players').append(avatarDiv);
                        }
                    }

                    if (obj.data.status == 'playing') {
                        // 开始比赛
                        for (i in intervalObjs) {
                            clearInterval(intervalObjs[i]);
                        }
                        // $('#match_btn').fadeIn();
                        // $('#timer').fadeOut();
                        if (obj.data.my_player.match_player_status == 3) {
                            $('.race-prepare').addClass('hide');
                            $('.start-race').removeClass('hide');
                            $('.start-race-disable').hide();
                        } else {
                            $('.race-prepare').addClass('hide');
                            $('.start-race-already').removeClass('hide');
                            $('.race-already').removeClass('hide');
                            $('.start-race-disable').hide();
                            $('.matching-race').addClass('hide');
                        }
                    } else if (obj.data.status == 'end') {
                        $('.race-prepare').addClass('hide');
                        $('.start-race-already').removeClass('hide');
                        $('.race-already').removeClass('hide');
                        $('.race-already').html('已经结束');
                        $('.start-race-disable').hide();
                    } else if (obj.data.status == 'matching') {
                        if (obj.data.my_player.match_player_status == 2) {
                            $('.race-prepare').addClass('hide');
                            $('.start-race').addClass('hide');
                            $('.start-race-disable').removeClass('hide');
                            $('.matching-race').addClass('hide');
                        } else {
                            $('.race-prepare').removeClass('hide');
                            $('.start-race-already').addClass('hide');
                            $('.race-already').addClass('hide');
                            $('.start-race-disable').addClass('hide');
                            $('.matching-race').removeClass('hide');
                            // $('.start-btn').addClass('hide');
                        }


                    }
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });
    }
</script>
