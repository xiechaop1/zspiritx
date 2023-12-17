$(function () {

  /*
    //浏览器是否支持语音输入判断
    if (document.createElement("input").webkitSpeech === undefined) {
        alert("很遗憾，你的浏览器不支持语音识别。");
    } else {
        alert("尝试使用语言识别来输入内容吧");
    }
*/
  /*  $(document).bind("ajaxSend", function() {
        $("#h5-process").modal('show');
        // $("#h5-process").show();
    }).bind("ajaxComplete", function() {
        $("h5-process").modal('hide');
    });*/
    // var old_answer_json=$("input[name='ask_old_answer']").val();

    //判断是否答对
    $(".ask_answer").click(function () {
        // submitAnswer($(this));
        submitAskAnswer();
    });
    $(".ask_answer_show").click(function () {
        $("#h5-process").modal("show");
    });
    $(".ask_answer_hide").click(function () {
        $("#h5-process").modal("hide");
    });

    $('#ask_form').submit(function() {
        submitAnswer($(this).find("input[name='ask_answer']"));
        return false;
    });
    $(".loading-box").click(function (){
         $(".loading-box").addClass('hide');  
         $("body").removeClass('modal-open'); 
    })

    function submitAskAnswer(thisObj) {
        var that=$("#answer-info");
        var story_id=that.attr("data-story");
        var user_id=$("input[name='user_id']").val();
        var old_answer=$("input[name='ask_old_answer']").val();
        var session_id=$("input[name='session_id']").val();
        var v_select = $("input[name='ask_answer_txt']").val();
        console.log("v_select:"+v_select,v_select.length);

        // $("#h5-process").modal('show');
        // $("#h5-process").show();

        // $("#answer-box").hide();
        if(v_select==null||v_select==undefined||v_select==''){
            $.alert('请选择答案')
            // $("#h5-null").modal('show');
        }


        if(v_select!=null&&v_select!=undefined&&v_select!=""){
            console.log('ajax 进程 1')
            // $("#h5-process").modal("show");
            $(".loading-box").removeClass('hide');
            $("body").addClass('modal-open');

            var content_obj = $('#answer-border-response');
            content_obj.html('');
            var newName = $('<span class="chat_name_m">');
            var newContent = $('<span class="chat_content_m">');
            newName.html('我');
            var newDiv = $('<div class="row chat_div_r">');
            newContent.html(v_select);
            // console.log(newContent);
            newDiv.append(newContent);
            newDiv.append(newName);
            content_obj.append(newDiv);
            console.log('ajax 进程 2')

            $.ajax({
                type: "POST", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: true,
                timeout: 0,
                url: '/ask/say?is_test=1',
                data:{
                    user_id:user_id,
                    answer:v_select,
                    story_id:story_id,
                    session_id:session_id,
                    old_answer:old_answer
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log('ajax 进程 3')
                    $("#h5-process").modal("hide");
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    // $.alert("网络异常，请检查网络情况");
                    $.alert(textStatus);
                },
                success: function (data, status){
                    console.log('ajax 进程 4')
                    // $("#h5-process").modal("hide");
                    $(".loading-box").addClass('hide');
                    $("body").removeClass('modal-open');

                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    //console.log("ajax请求成功:"+data.toString())


                    //新消息获取成功
                    if(obj["code"]==200){
                            // console.log(obj);
                            var old_answer_json=obj.data.old_answer_json;
                            var old_answer=obj.data.old_answer;
                            // var content_obj = $('#answer-border-response');
                            // content_obj.html('');
                            $("input[name='ask_old_answer']").val(old_answer_json);
                            var newName = $('<span class="chat_name_o">');
                            var newContent = $('<span class="chat_content_o">');
                            newName.html('小灵镜');
                            var newDiv = $('<div class="row chat_div_l">');
                            newContent.html(obj.data.msg);
                            // console.log(newContent);
                            newDiv.append(newName);
                            newDiv.append(newContent);
                            content_obj.append(newDiv);
                            // console.log(old_answer);
                            // for (oaId in old_answer) {
                            //     var oa = old_answer[oaId];
                            //     if (oa.role == "assistant") {
                            //         var newName = $('<span class="chat_name_o">');
                            //         var newContent = $('<span class="chat_content_o">');
                            //         newName.html('小灵镜');
                            //         var newDiv = $('<div class="row chat_div_l">');
                            //         newContent.html(oa.content);
                            //         // console.log(newContent);
                            //         newDiv.append(newName);
                            //         newDiv.append(newContent);
                            //     } else {
                            //         var newName = $('<span class="chat_name_m">');
                            //         var newContent = $('<span class="chat_content_m">');
                            //         newName.html('我');
                            //         var newDiv = $('<div class="row chat_div_r">');
                            //         newContent.html(oa.content);
                            //         // console.log(newContent);
                            //         newDiv.append(newContent);
                            //         newDiv.append(newName);
                            //     }
                            //     // $(content_obj).html(content_obj.html() + newDiv);
                            //     content_obj.append(newDiv);
                            // }
                            // content_obj.scrollTop(content_obj.prop("scrollHeight"));
                            if (obj.data.voice != undefined) {
                                var audio_voice=$("#audio_voice")[0];
                                audio_voice.src = obj.data.voice;
                                audio_voice.play();
                            }
                        $("input[name='ask_answer_txt']").val('');
                            return false;
                            // $.alert(obj.data.msg);


                    }
                    //新消息获取失败
                    else{

                        $.alert(obj.msg);
                    }

                }
            });
        }
    };

    $("#qa_return_btn").click(function (){
        // Unity.call('WebViewOff&FalseAnswer');
        var params = {
            'WebViewOff':1,
            'AnswerType':2
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });


})