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
    }
</style>
<input type="hidden" name="match_id" value="<?= $matchId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="join_expire_time" value="<?= !empty($storyMatch->join_expire_time) ? $storyMatch->join_expire_time : 0 ?>">

<div class="w-100 m-auto" style="top: 20px;">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        匹配中
                    </div>
                    <div id="players">
                        <div style="float: left; width: 50px;"></div>
                    </div>

                </div>
            </div>
            <div class="m-t-30 bold btn-m-green" id="timer">

            </div>
            <div id="match_btn" class="btn-m-green m-t-30 float-right m-r-20" style="display: none;">
                <a href="/matchh5/knockout?user_id=<?= $userId ?>&session_id=<?= $sessionId ?>&story_id=<?= $storyId ?>&match_id=<?= $matchId ?>&qa_id=<?= $qaId ?>">
                开始比赛
                </a>
            </div>
        </div>
        </div>

    </div>

</div>
<script>
    window.onload = function(){
        var matching = setInterval(function(){
            getKnockoutStatus(matching);
            // clearInterval(matching);
        }, 1000);
        var timer = setInterval(function(){
            computeTimer();
        }, 1000);
    };

    function computeTimer() {
        // 获取当前时间
        var nowTime = new Date();
        var timeInt = parseInt(nowTime.getTime()/1000);
        var cha = $('input[name="join_expire_time"]').val() - timeInt;

        if (cha > 0) {
            $('#timer').html('倒计时：' + cha + 's');
        } else {
            $('#timer').fadeOut();
            $('#match_btn').fadeIn();
        }
    }

    var playerIds = new Array();
    function getKnockoutStatus(intervalObj) {
        var match_id = $('input[name="match_id"]').val();
        var story_id = $('input[name="story_id"]').val();
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/match/get_knockout_status',
            data:{
                // user_id:user_id,
                story_id:story_id,
                match_id:match_id,
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
                    if (obj.data.players.length > 0) {
                        for (i in obj.data.players) {
                            if (playerIds.indexOf(obj.data.players[i].user.id) > -1) {
                                continue;
                            }
                            var playerAvatar = obj.data.players[i].user.avatar;
                            console.log(obj.data.players[i].user.id);
                            playerIds.push(obj.data.players[i].user.id);
                            var avatarDiv = '<div id="avatar_' + obj.data.players[i].user.id + '" class="w-100 d-flex m-b-10 avatar"><img src="' + playerAvatar + '" width="100"></div>';
                            $('#players').append(avatarDiv);
                        }
                    }
                    if (obj.data.status == 'playing') {
                        // 开始比赛
                        clearInterval(intervalObj);
                        $('#match_btn').fadeIn();
                        $('#timer').fadeOut();
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