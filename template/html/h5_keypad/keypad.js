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

        var answerType = $("input[name='answer_type']").val();
        console.log(answerType);
        if (answerType > 0) {
            var params = {
                'WebViewOff': 1,
                'AnswerType': answerType
            }

        } else {
            var params = {
                'WebViewOff': 1
            }
        }
        console.log(params);
        var data = $.toJSON(params);
        Unity.call(data);
    });

    $(".keypadinfo").slideDown(300);
    var $keypadNum = $("#keypadNum");
    $("#keypadNum").focus(function () {
        $(".keypadinfo").slideDown(300);
        document.activeElement.blur();
    });
    $(".keypadnum").each(function () {
        $(this).click(function () {
            if (($keypadNum.text()).indexOf(".") != -1 && ($keypadNum.text()).substring(($keypadNum.text()).indexOf(".") + 1, ($keypadNum.text()).length).length == 2) {
                return;
            }
            if ($.trim($keypadNum.text()) == "0") {
                return;
            }
            if (parseInt($keypadNum.text()) > 10000000000 && $keypadNum.text().indexOf(".") == -1) {
                return;
            }
            $keypadNum.text($keypadNum.text() + $(this).text());
        });
    });

    $("#keypad-return").click(function () {
        $keypadNum.text(($keypadNum.text()).substring(0, ($keypadNum.text()).length - 1));
        if (!$keypadNum.text()) {
            $('.keypad').addClass('keypad-disabled').find('a').attr('href', 'javascript:return false;');
        }
    });

    $("#keypad-zero").click(function () {
        if (($keypadNum.text()).indexOf(".") != -1 && ($keypadNum.text()).substring(($keypadNum.text()).indexOf(".") + 1, ($keypadNum.text()).length).length == 2) {
            return;
        }
        if ($.trim($keypadNum.text()) == "0") {
            return;
        }
        if (parseInt($keypadNum.text()) > 10000 && $keypadNum.text().indexOf(".") == -1) {
            return;
        }
        $keypadNum.text($keypadNum.text() + $(this).text());
    });

    $("#keypad-float").click(function () {
        if ($.trim($keypadNum.text()) == "") {
            return;
        }

        if (($keypadNum.text()).indexOf(".") != -1) {
            return;
        }

        if (($keypadNum.text()).indexOf(".") != -1) {
            return;
        }
        $keypadNum.text($keypadNum.text() + $(this).text());
    });
    $('.keypad').click(function () {
        if($keypadNum.text()>999){
            var story_id=$("input[name='story_id']").val();
            var user_id=$("input[name='user_id']").val();
            var qa_id=$("input[name='qa_id']").val();
            if(story_id==0){
                story_id=10;
            }
            if(user_id==0){
                user_id=1;
            }

            var phone=$keypadNum.text();
            //audio 素材
            var audio_wait=$("#audio_right")[0];
            audio_wait.play();
            setTimeout(function (){
                audio_wait.pause();
            },2000)

            $("#keypad-open").hide();
            $("#keypad-close").show();

            // $("#audio_wrong").prop("src","https://zspiritx.oss-cn-beijing.aliyuncs.com/voice/phone/no_phone_number.mp3");
            // var audio=$("#audio_wrong")[0];
            // audio.play();

            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/process/phone_call',
                data:{
                    is_test:1,
                    user_id:user_id,
                    story_id:story_id,
                    qa_id:qa_id,
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
                    var voice;
                    var cont = 0;
                    if(obj["code"]==200){
                        console.log(obj.data);

                        var  i = 0;
                        var num=obj.data.voices.length;//音频长度

                        // Change the audio element source
                        var voice = obj.data.voices[i];
                        $("#audio_wrong").prop("src", voice);
                        var audio = $("#audio_wrong")[0];

                        // audio.play();

                        console.log(voice,num,i);

                        // $("#audio_wrong").prop("src", obj.data.voice);

                        //拨打电话铃声响2000ms后执行播报；
                        setTimeout(function (){
                            next(i,obj.data.voices,num);

                            // audio.play();
                            // audio.addEventListener('ended', function () {
                            //     if(i==num-1){
                            //         $("#keypad-open").show();
                            //         $("#keypad-close").hide();
                            //     }else{
                            //         i = i+1;
                            //         console.log("歌曲编号：",i);
                            //         voice = obj.data.voices[i];
                            //         $("#audio_wrong").prop("src", voice);
                            //         var audio = $("#audio_wrong")[0];
                            //         console.log(voice,num);
                            //     }
                            // });

                        },2000);



                        // for (vid in obj.data.voices) {
                        //     voice = obj.data.voices[vid];
                        //     $("#audio_wrong").prop("src", voice);
                        //     var audio = $("#audio_wrong")[0];
                        //     console.log(voice);
                        //     // $("#audio_wrong").prop("src", obj.data.voice);
                        //     audio.play();
                        //
                        //     audio.addEventListener('ended', function () {
                        //         cont = cont+1;
                        //     });
                        //     while(cont == 0) {
                        //
                        //     }
                        // }


                        if (obj.data.value > 0) {
                            $("input[name='answer_type']").val(obj.data.value);
                        } else {
                            $("input[name='answer_type']").val(2);
                        }

                        if (obj.data.after > 0) {
                            $("input[name='after_close']").val(obj.data.after);
                        } else {
                            $("input[name='after_close']").val(0);
                        }

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
            $(".toast").empty().text("请输入手机号");
            $(".toast-box").show();
            setTimeout(function (){
                $(".toast-box").hide()
            },1800)
        }

        // alert("拨打电话"+$keypadNum.text())
    });

    //Music change
    function next(i,list,num){
        if(i==num){
            $("#keypad-open").show();
            $("#keypad-close").hide();
        }else{
            var voice = list[i];
            $("#audio_wrong").prop("src", voice);
            var audio = $("#audio_wrong")[0];
            audio.play();
            console.log("歌曲编号：",i,voice,num);

            audio.addEventListener('ended', function () {
                i = i+1;
                next(i,num,list);
            });
        }

    }

    $('.keypad-close').click(function () {
        $("#keypad-open").show();
        $("#keypad-close").hide();
        //audio 素材
        var audio_wait=$("#audio_right")[0];
        var audio_wrong=$("#audio_wrong")[0];
        audio_wait.pause();
        audio_wrong.pause();
        $keypadNum.text('');

        // alert("挂电话")
        var after = $("input[name='after_close']").val();
        if (after == 1) {
            var answerType = $("input[name='answer_type']").val();
            console.log(answerType);
            if (answerType > 0) {
                var params = {
                    'WebViewOff': 1,
                    'AnswerType': answerType
                }
            } else {
                var params = {
                    'WebViewOff': 1
                }
            }
            console.log(params);
            var data = $.toJSON(params);
            Unity.call(data);
        }
    });
})