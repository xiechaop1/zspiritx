$(function () {

    $(document).bind("ajaxSend", function() {
        $("#h5-process").modal('show');
        // $("#h5-process").show();
    }).bind("ajaxComplete", function() {
        $("h5-process").modal('hide');
    });
    // var old_answer_json=$("input[name='ask_old_answer']").val();

    //判断是否答对
    $("input[name='ask_answer']").click(function () {
        submitAnswer($(this));
    });

    $('#ask_form').submit(function() {
        submitAnswer($(this).find("input[name='ask_answer']"));
        return false;
    });

    function submitAnswer(thisObj) {
        var that=$("#answer-info");
        var story_id=that.attr("data-story");
        var user_id=$("input[name='user_id']").val();
        var old_answer=$("input[name='ask_old_answer']").val();
        var session_id=$("input[name='session_id']").val();
        var v_select = $("input[name='ask_answer_txt']").val();

        // $("#h5-process").modal('show');
        // $("#h5-process").show();

        // $("#answer-box").hide();
        if(v_select==null){
            $("#h5-null").modal('show');
        }


        if(v_select!=null){

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

            $.ajax({
                type: "POST", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                timeout: 120000,
                url: '/ask/say?is_test=1',
                data:{
                    user_id:user_id,
                    answer:v_select,
                    story_id:story_id,
                    session_id:session_id,
                    old_answer:old_answer
                },
                onload: function (data) {

                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $("#h5-process").modal('hide');
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    // $.alert("网络异常，请检查网络情况");
                    $.alert(textStatus);
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    //console.log("ajax请求成功:"+data.toString())

                    $("#h5-process").modal('hide');
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
                        $.alert(obj.msg)
                    }

                }
            });
        }
    };



})