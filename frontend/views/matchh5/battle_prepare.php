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
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="match_id" value="<?= $matchId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">

<div class="w-100 m-auto">
    <!--组队倒计时时间-->
    <input type="hidden" name="countdown" value="10">

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
        <div class="match-text-box m-t-50">
            <div class="match-title1 start-race">
                匹配成功
            </div>

            <div class="match-title1" style="color:#4DAF51; margin-left: 50px; margin-right: 50px; text-align: left; margin-top: 15px; line-height: 120%;font-size:24px;">

            </div>

            <div class="match-title1  start-race-disable hide">
                匹配中(<span class="match-title-tag-1">1</span>/<span class="match-title-tag-2">30</span>)
            </div>
            <div class="match-text1 m-t-20 start-race-disable hide">
                倒计时：<span class="match-text-tag-1">115</span>s
            </div>
        </div>

        <div class="text-center m-t-80 m-b-20">
            <span id="start_btn">
                <label class="btn-green-m active  start-race" id="battle_pre">开始战斗</label>
            </span>

        </div>

    </div>
</div>

<div class="w-100 m-auto" style="top: 20px; display:none;">

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
            <div class="btn-m-green m-t-30 float-right m-r-20" id="battle_pre1">
<!--                <a href="/matchh5/battle?user_id=--><?php //= $userId ?><!--&session_id=--><?php //= $sessionId ?><!--&story_id=--><?php //= $storyId ?><!--&match_id=--><?php //= $matchId ?><!--">-->
                开始战斗
<!--                </a>-->
            </div>
        </div>
        </div>

    </div>

</div>

<script>
    window.onload = function () {
        $('#battle_pre').click(function () {
            var user_id = $('input[name="user_id"]').val();
            var session_id = $('input[name="session_id"]').val();
            var story_id = $('input[name="story_id"]').val();
            var match_id = $('input[name="match_id"]').val();
            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: true,
                url: '/match/battle_for_u3d',
                data:{
                    story_id:story_id,
                    user_id:user_id,
                    session_id:session_id,
                    match_id:match_id,
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
                    console.log(ajaxObj);
                    if(ajaxObj["code"]==200){
                        var params = {
                            'WebViewOff': 1
                        }
                        var data=$.toJSON(params);
                        Unity.call(data);
                    }
                    //新消息获取失败
                    else{
                        $.alert(ajaxObj.msg)
                    }

                }
            });
        });
    }
</script>
