$(function () {
    <!--自适应布局-->
    (function () {
        var designW = 750;  //设计稿宽
        var font_rate = 100;
        //适配
        document.getElementsByTagName("html")[0].style.fontSize = document.body.offsetWidth / designW * font_rate + "px";
        document.getElementsByTagName("body")[0].style.fontSize = document.body.offsetWidth / designW * font_rate + "px";

        //监测窗口大小变化
        window.addEventListener("onorientationchange" in window ? "orientationchange" : "resize", function () {
            document.getElementsByTagName("html")[0].style.fontSize = document.body.offsetWidth / designW * font_rate + "px";
            document.getElementsByTagName("body")[0].style.fontSize = document.body.offsetWidth / designW * font_rate + "px";
        }, false);
    })();

    $("#return_btn").click(function (){
        var params = {
            'WebViewOff':1,
            'AnswerType':2
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $(".keypadinfo").slideDown(300);
    var $keypadNum = $("#keypadNum");
    $("#keypadNum").focus(function () {
        $(".keypadinfo").slideDown(300);
        document.activeElement.blur();
    });

    $('.pay').click(function () {
        var num=$(this).attr("data-val");
        if(num>0){
            var story_id=$("input[name='story_id']").val();
            var user_id=$("input[name='user_id']").val();

            $("#keypad-open").hide();
            $("#keypad-close").show();

            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/process/phone_call',
                data:{
                    is_test:1,
                    user_id:user_id,
                    story_id:story_id,
                    phone:phone
                },
                onload: function (data) {
                    $('#answer-border-response').html('处理中……');
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    $(".toast").empty().text("网络异常，请检查网络情况");
                    $(".toast-box").show();
                    $("#keypad-open").show();
                    $("#keypad-close").hide();
                    setTimeout(function (){
                        $(".toast-box").hide()
                    },1800)
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    //console.log("ajax请求成功:"+data.toString())

                    //新消息获取成功
                    if(obj["code"]==200){

                        window.open("https://www.jb51.net");


                        $("#audio_wrong").prop("src",obj.data);
                        var audio=$("#audio_wrong")[0];
                        audio.play();
                        setTimeout(function (){
                            audio.play();

                            audio.addEventListener('ended', function() {
                                $("#keypad-open").show();
                                $("#keypad-close").hide();
                            });
                        },2000)
                    }
                    //新消息获取失败
                    else{
                        $(".toast").empty().text(obj.msg);
                        $(".toast-box").show();
                        $("#keypad-open").show();
                        $("#keypad-close").hide();
                        setTimeout(function (){
                            $(".toast-box").hide()
                        },1800)
                    }
                }
            });
        }
        else{
            $(".toast").empty().text("网络异常，请刷新后重试");
            $(".toast-box").show();
            setTimeout(function (){
                $(".toast-box").hide()
            },1800)
        }

        // alert("拨打电话"+$keypadNum.text())
    });
})