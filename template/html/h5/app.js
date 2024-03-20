$(function () {
    //关闭提示信息
    $(".close-note").on('click',function () {
        var me=$(this);
        me.closest('.note').remove();
    });

    //打开modal
    $(".open-modal").on('click',function () {
        var me=$(this);
        var dataId=me.attr('data-id');
        $("#"+dataId).modal('show');
    })

    //页面重新加载
    $(".window-reload").on('click',function () {
        window.location.reload()
    })

    $('.code-input input').on('keyup', function(e) {
        // 转换为大写
        this.value = this.value.toUpperCase();

        var oldVal = $(this).val();
        if (e.key.length === 1 && e.key.match(/[a-zA-Z0-9]/)) {
            $(this).next('input').focus();
            $(this).next('input').select();
        } else if (e.key === 'Backspace') {
            if(oldVal=='') {
                $(this).prev('input').focus();
                // $(this).prev('input').val('');
            }
        } else if (e.key == 'ArrowLeft') {
            $(this).prev('input').focus();
            $(this).prev('input').select();
        } else if (e.key == 'ArrowRight') {
            $(this).next('input').focus();
            $(this).next('input').select();
        }
    });

    $(".selection-btn").click(function () {
        var val = $(this).attr('answer_type');
        var type = $(this).attr('selection_type');

        if (type == 1) {
            var params = {
                'WebViewOff': 1,
                'AnswerType': val
            }
            console.log(params);
            var data = $.toJSON(params);
            Unity.call(data);
        }
    });

    //判断是否答对
    $("input[name='answer']").click(function () {
        submitAnswer($(this));
    });

    $(".verify_code input[name='answer_txt']").change(function () {
        var v_selects = $("input[name='answer_txt']");
        for (i = 0; i < v_selects.length; i++) {
            if (v_selects[i].value == '') {
                return false;
            }
        }
        submitAnswer($(this));
    });

    function submitAnswer(thisObj) {
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
        if (qa_type == 1 || qa_type == 2 || qa_type == 3 || qa_type == 4) {
            var v_select = $("input[name='answer']:checked").val();
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
        // $("#answer-box").hide();
        if(v_select==null){
            $("#h5-null").modal('show');
        }


        if(v_select!=null){

            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/qa/add_user_answer',
                data:{
                    user_id:user_id,
                    qa_id:qa_id,
                    answer:v_select,
                    story_id:story_id,
                    session_id:session_id,
                    session_stage_id:session_stage_id,
                    begin_ts:begin_ts
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

                    //audio 素材
                    var audio_right=$("#audio_right")[0];
                    var audio_wrong=$("#audio_wrong")[0];

                    //新消息获取成功
                    if(obj["code"]==200){
                        if (qa_type == 9) {
                            console.log(obj);
                            // $.alert(obj.data.msg);
                            var htmlObj = $('#answer-border-response');
                            if (htmlObj.html() == '等待提问……') {
                                htmlObj.html('');
                            }
                            htmlObj.html(htmlObj.html() + '<br>' + '小灵语：' + obj.data.msg);
                            if (obj.data.voice != undefined) {
                                var audio_voice=$("#audio_voice")[0];
                                audio_voice.src = obj.data.voice;
                                audio_voice.play();
                            }
                            $("input[name='answer_txt']").val('');
                            return false;
                        }
                        if(v_ture==v_select){
                            if (obj.data.score.score != undefined) {
                                var score_text = "+" + obj.data.score.score + "枚";
                                if (obj.data.score.addition > 0) {
                                    score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                                }
                                $("#gold_score").html(score_text);
                            }
                            $("#answer-box").hide();
                            $("#answer-right-box").removeClass('hide');
                            // $("#h5-right").modal('show');
                            audio_right.play();
                            setTimeout(function (){
                                // Unity.call('WebViewOff&TrueAnswer');
                                var params = {
                                    'WebViewOff':1,
                                    'AnswerType':1
                                }
                                var data=$.toJSON(params);
                                Unity.call(data);
                            },2000);
                        }
                        else{
                            $("#answer-box").hide();
                            $("#answer-error-box").removeClass('hide');
                            // $("#h5-worry").modal('show');
                            audio_wrong.play();
                            setTimeout(function (){
                                // Unity.call('WebViewOff&FalseAnswer');
                                // var params = {
                                //     'WebViewOff':1,
                                //     'AnswerType':2
                                // }
                                // var data=$.toJSON(params);
                                // Unity.call(data);
                                location.reload();
                            },2000);
                        }
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
    };

    $("input[name='baggage']").click(function ()
    {
        var that=$("#answer-info");
        var user_id=$("input[name='user_id']").val();
        var story_id=$("input[name='story_id']").val();
        var session_id=$("input[name='session_id']").val();
        var target_story_model_id=$("input[name='target_story_model_id']").val();
        var target_story_model_detail_id=$("input[name='target_story_model_detail_id']").val();
        var target_model_id=$("input[name='target_model_id']").val();
        var v_ture=that.attr("data-value");
        var v_detail=that.attr("data-detail");
        var v_select=$("input[name='baggage']:checked").val();
        // $("#answer-box").hide();
        if(v_select==null){
            $("#h5-null").modal('show');
        }


        if(v_select!=null){
            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/process/use_model',
                data:{
                    user_id:user_id,
                    story_id:story_id,
                    session_id:session_id,
                    user_model_id:v_select,
                    target_story_model_id:target_story_model_id,
                    target_story_model_detail_id:target_story_model_detail_id,
                    target_model_id:target_model_id,
                    is_test:1
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
                        if (obj.data.type == 1) {
                            var params = obj.data.ret;
                            // var data = $.toJSON(params);
                            var data = params;
                            console.log(data);
                            Unity.call(data);
                        } else if (obj.data.type == 5) {
                            // 如果是展现，则直接展现
                            $('#baggage_title').html(obj.data.title);
                            $('#baggage_html').html(obj.data.html);
                            $('#baggage_desc').html(obj.data.desc);
                            var obj = $('#baggage_detail');
                            // $('#baggage_detail_back').modal('show');
                            // obj.show();
                            obj.modal('show');
                        }  else if (obj.data.type == 6) {
                            // 如果是展现，则直接展现
                            $('#baggage_title').html(obj.data.title);
                            $('#baggage_html').html(obj.data.html);
                            $('#baggage_desc').html(obj.data.desc);
                            var obj = $('#baggage_detail');
                            // $('#baggage_detail_back').modal('show');
                            // obj.show();
                            obj.modal('show');
                        } else {
                            // if(v_ture==v_select){
                            $.alert('使用成功！');
                            var params = {
                                'WebViewOff': 1
                            }
                            var data=$.toJSON(params);
                            Unity.call(data);
                            // setTimeout(function () {
                            //     window.location.reload();
                            // }, 3000);
                        }

                        // }
                        // else{
                        //     $.alert('使用失败！');
                        // }
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
    })

    var height = $(window).height();
    $("#myCarousel .item,#banner .item").css('height',height+'px')

    $('.owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        // nav:true,
        items:1,

        // responsive:{
        //     0:{
        //         items:1
        //     },
        //     600:{
        //         items:3
        //     },
        //     1000:{
        //         items:5
        //     }
        // }
    })

    $(".owl-carousel .logout_btn").click(function() {
        var unityVersion = $('#unity_version').val();
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/user/logout',
            data:{},
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //新消息获取成功
                if(obj["code"]==200){
                    location.href='/passport/web_login?unity_version=' + unityVersion;
                }
                //新消息获取失败
                else{
                    alert(obj.msg)
                }

            }
        });

    });
    $(".owl-carousel .show_detail").click(function() {
       var obj_tar = $(this).attr('d-target');
       var obj = $('#story_detail_2');
       console.log(obj);
       obj.show();
       obj.attr('display', 'block');
         // obj.modal('show');
       // console.log($(obj_tar));
       // $(obj_tar).modal('show');
    });
    $(".owl-carousel .play_btn").click(function() {
        var t = $(this).parent().parent().parent();
        var isDebug = t.find("input[name='isDebug']").val();
        var storyId = t.find("input[name='storyId']").val();
        var userId = $('#user_id').val();
        var orderStatus = t.find("input[name='orderStatus']").val();
        $('#login_is_debug').val(isDebug);
        $('#login_story_id').val(storyId);

        var userId = $('#user_id').val();
        var unityVersion = $('#unity_version').val();

        if (orderStatus == 0) {
            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/order/create',
                data:{
                    user_id:userId,
                    story_id:storyId,
                    is_test:1
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    $.alert("网络异常，请检查网络情况");
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    console.log(obj);
                    //console.log("ajax请求成功:"+data.toString())
                    //新消息获取成功
                    if(obj["code"]==200){
                        console.log(obj.data);
                        var order_status = obj.data.order_status;
                        if (order_status != 0 && (order_status == 1 || order_status == 2)) {
                            if (unityVersion != "") {
                                var params = {
                                    'WebViewOff': 1,
                                    'DebugInfo': isDebug,
                                    'UserId': userId,
                                    'StoryId': storyId
                                }
                                var data = $.toJSON(params);
                                console.log(data);
                                Unity.call(data);
                            } else {
                                alert('购买成功！');
                            }
                        } else {
                            // 执行支付唤醒
                            alert('准备支付');
                        }
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });

            return false;
        }

        if (userId != 0) {
            if (unityVersion != "") {
                var params = {
                    'WebViewOff': 1,
                    'DebugInfo': isDebug,
                    'UserId': userId,
                    'StoryId': storyId
                }
                var data = $.toJSON(params);
                console.log(data);
                Unity.call(data);
            } else {
                alert('已经购买！');
            }
        } else {
            $('#loginform').show();
        }
    });
    $("#login_return_btn").click(function() {
        $("#loginform").hide();
    });
    $('#get_verifycode').click(function() {
        var par = $(this).parent();
        // 30秒倒计时
        var mobile=$("input[name='mobile']").val();
        if (mobile == "" || mobile == null) {
            alert("请输入手机号");
            return false;
        }
        if(mobile!=null){
            $.ajax({
                type: "GET",
                dataType: "json",
                async: false,
                url: '/passport/verification-code',
                data:{
                    mobile:mobile,
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    alert("网络异常，请检查网络情况");
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    if (data["code"] == 200) {
                        alert("验证码已发送，请注意查收");
                        par.find('a').addClass('disabled');
                        var time = 30;
                        var timer = setInterval(function () {
                            if (time > 0) {
                                time--;
                                par.find('a').text(time);
                            } else {
                                clearInterval(timer);
                                par.find('a').text('获取验证码');
                                par.find('a').removeClass('disabled');
                            }
                        }, 1000);
                    } else {
                        alert(data["msg"]);
                    }

                }
            });
        }
    });
    $("#login_btn").click(function ()
    {
        var mobile=$("input[name='mobile']").val();
        var verifycode=$("input[name='verifycode']").val();
        var isDebug = $('#login_is_debug').val();
        var storyId = $('#login_story_id').val();
        var userId = $('#user_id').val();
        var isagree = $('#agreement1').is(':checked');
        var unityVersion = $('#unity_version').val();

        if (mobile.length > 3 && isagree == false) {
            alert('请勾选用户协议');
            return false;
        }

        if(mobile!=null){
            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/user/login_and_reg_by_mobile',
                data:{
                    mobile:mobile,
                    verify_code:verifycode,
                    is_test:1
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    $.alert("网络异常，请检查网络情况");
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    console.log(obj);
                    //console.log("ajax请求成功:"+data.toString())
                    //新消息获取成功
                    if(obj["code"]==200){
                        if (unityVersion != "") {
                            var params = {
                                'UserId': obj["data"]["id"],
                            }
                            var data = $.toJSON(params);
                            console.log(data);
                            Unity.call(data);
                        }
                        location.href="/home/index?unity_version=" + unityVersion;
                        // var params = {
                        //     'WebViewOff':1,
                        //     'DebugInfo':isDebug,
                        //     'UserId': obj.data.id,
                        //     'StoryId': storyId
                        // }
                        // var data=$.toJSON(params);
                        // // var data = eval( "{" + paramsjson + "}" );
                        //
                        // // Unity.call('{'WebViewOff':1, 'DebugInfo':isDebug, 'UserId': obj.data.id, 'StoryId': storyId }');
                        // Unity.call(data);
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
    });

    $("#dialog_return_btn").click(function (){
        var tar_id = $(this).attr('target_id');
        var dialog = $('#' + tar_id);
        // dialog.hide();
        dialog.modal('hide');
    });

    $("#return_btn").click(function (){
        var params = {
            'WebViewOff':1
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $("#qa_return_btn").click(function (){
        // Unity.call('WebViewOff&FalseAnswer');
        var rtnAnswerType = $('#rtn_answer_type').val();
        var params = {
            'WebViewOff':1,
            'AnswerType':rtnAnswerType
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $("#guide_confirm_return_btn").click(function (){
        // Unity.call('WebViewOff&TrueAnswer');

        var params = {
            'AnswerType':1,
            'WebViewOff':1
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $("#msg_return_btn").click(function (){
        // Unity.call('WebViewOff&TrueAnswer');

        var answerType = $(this).attr('answer_type');
        if (answerType == undefined) {

            var params = {
                'WebViewOff': 1
            }
        } else {
            var params = {
                'WebViewOff': 1,
                'AnswerType': answerType
            }
        }
        // console.log(params);
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $(".knowledge-title").click(function (){
        var obj = $(this);
        showKnowledge(obj);

       //  var tobj = $(this).parent().find(".knowledge-content");
       //  var allobj = $(".knowledge-content");
       //
       //  if (tobj.is(':hidden')) {
       //      allobj.hide();
       //     tobj.show();
       // } else {
       //     tobj.hide();
       // }
        // $(".knowledge-content").hide();
    });

    function showKnowledge(obj) {
        var knowledge_title = obj.find("input[NAME='knowledge_title']").val();
        var knowledge_image = obj.find("input[NAME='knowledge_image']").val();
        var knowledge_desc_code = obj.find("input[NAME='knowledge_content']").val();

        $('#knowledge_title').html(knowledge_title);
        if (knowledge_image != '') {
            $('#knowledge_image').html('<img src=' + knowledge_image + ' style="width: 100%; height: auto;">');
        } else {
            $('#knowledge_image').html('');
        }
        var knowledge_desc = unescape(knowledge_desc_code);
        $('#knowledge_desc').html(knowledge_desc);

        var user_knowledge_id = obj.find("input[NAME='user_knowledge_id']").val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/knowledge/set_read',
            data:{
                user_knowledge_id:user_knowledge_id
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //新消息获取成功
                if(obj["code"]==200){
                    $('#unread_' + user_knowledge_id).hide();
                }
                //新消息获取失败
                else{
                    alert(obj.msg)
                }

            }
        });

        $('#knowledge_detail').modal('show');
    }

    $(".keyboard").click(function (){
        var obj = $(this);
        var val = obj.attr('val');
        var input_obj = obj.parent().parent().find("input[NAME='answer_txt']");
        if (val != 'DELETE') {
            input_obj.val(input_obj.val() + val);
        } else {
            input_obj.val(input_obj.val().slice(0, -1));
        }
    });

    $(".v_div_keyboard").click(function() {
        var obj = $(this);
        var val = obj.attr('val');
        var input_obj = obj.parent().parent().find("input[NAME='answer_txt']");
        if (val != 'DELETE') {
            var j=0;
            // 定义一个数组
            var list = new Array();
            for (i=0; i<input_obj.length; i++) {
                if ($(input_obj[i]).val() =="" ) {
                    $(input_obj[i]).val(val);
                    return true;
                    // list.push(i);
                    // j++;
                    // if (j == val.length) {
                    //     console.log(list);
                    //     for (k=0; k<list.length; k++) {
                    //         $(input_obj[list[k]]).val(val[k]);
                    //     }
                    //     return true;
                    // }
                } else {
                    j = 0;
                    list = [];
                }
            }
        } else {
            for (i=input_obj.length - 1; i>=0; i--) {
                if ($(input_obj[i]).val() !="" ) {
                    $(input_obj[i]).val('');
                    return true;
                }
            }
        }
    });

    $(".v_keyboard").click(function (){
        var obj = $(this);
        var val = obj.attr('val');
        var input_obj = obj.parent().parent().find("input[NAME='answer_txt']");

        if (val != 'DELETE') {
            var j=0;
            // 定义一个数组
            var list = new Array();
            for (i=0; i<input_obj.length; i++) {
                if ($(input_obj[i]).val() =="" ) {
                    $(input_obj[i]).val(val);
                    return true;
                    // list.push(i);
                    // j++;
                    // if (j == val.length) {
                    //     console.log(list);
                    //     for (k=0; k<list.length; k++) {
                    //         $(input_obj[list[k]]).val(val[k]);
                    //     }
                    //     return true;
                    // }
                } else {
                    j = 0;
                    list = [];
                }
            }
        } else {
            for (i=input_obj.length - 1; i>=0; i--) {
                if ($(input_obj[i]).val() !="" ) {
                    $(input_obj[i]).val('');
                    return true;
                }
            }
        }
    });

    $(".v_s_keyboard").click(function (){
        var sudoku_current = $('#sudoku_current').val();
        $('.v_s_keyboard_choosen').removeClass('v_s_keyboard_choosen');
        $('.DELETE_v_s_keyboard_choosen').removeClass('DELETE_v_s_keyboard_choosen');
        var thisVal = $(this).attr('val');
        if (sudoku_current != thisVal) {
            $('#sudoku_current').val(thisVal);
            console.log(thisVal);
            if (thisVal != 'DELETE') {
                $(this).addClass('v_s_keyboard_choosen');
            } else {
                $(this).addClass('DELETE_v_s_keyboard_choosen');
            }
        } else {
            $('#sudoku_current').val('');
            // $(this).addClass('v_s_keyboard_choosen');
        }
    });

    $(".puzzle_sudoku_item").click(function() {
        var sudokuCurrent = $('#sudoku_current').val();
        var sudokuSize = $('#sudoku_size').val();
        var readOnly = $(this).attr('ro');

        var user_id=$("input[name='user_id']").val();
        var story_id=$("input[name='story_id']").val();
        var session_id=$("input[name='session_id']").val();
        var session_stage_id=$("input[name='session_stage_id']").val();
        var qa_id=$("input[name='qa_id']").val();
        var begin_ts=$("input[name='begin_ts']").val();

        console.log(readOnly);

        if (readOnly == '1') {
            return false;
        }

        if (sudokuCurrent == '') {
            return false;
        }
        if (sudokuCurrent == 'DELETE') {
            $(this).html(' ');
        } else {
            $(this).html(sudokuCurrent);
        }

        if (isValidSudoku(sudokuSize) == true) {
            console.log('success');
            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/qa/add_user_answer',
                data:{
                    user_id:user_id,
                    qa_id:qa_id,
                    answer:'True',
                    story_id:story_id,
                    session_id:session_id,
                    session_stage_id:session_stage_id,
                    begin_ts:begin_ts
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
                        $('.puzzle_check').unbind('click');
                        $('.puzzle_check').removeClass('puzzle_item');
                        $('.puzzle_check').addClass('puzzle_item_end');
                        if (obj.data.score.score != undefined) {
                            var score_text = "+" + obj.data.score.score + "枚";
                            if (obj.data.score.addition > 0) {
                                score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                            }
                            $("#gold_score").html(score_text);
                        }
                        // var audio_right=$("#audio_right")[0];
                        // audo_right.play();
                        $("#answer-box").removeClass('hide');
                        $("#answer-right-box").removeClass('hide');

                        // $("#h5-right").modal('show');
                        // setTimeout(function (){
                        //     // Unity.call('WebViewOff&TrueAnswer');
                        //     var params = {
                        //         'WebViewOff':1,
                        //         'AnswerType':1
                        //     }
                        //     var data=$.toJSON(params);
                        //     Unity.call(data);
                        // },3000)
                        setTimeout(function () {
                            // Unity.call('WebViewOff&TrueAnswer');
                            // var params = {
                            //     'WebViewOff':1,
                            //     'AnswerType':1
                            // }
                            // var data=$.toJSON(params);
                            // Unity.call(data);
                            $("#answer-right-box").addClass('hide');
                        }, 4000);
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
    });

    var isValidSudoku = function(size) {
        const [row, col, boxes] = [{}, {}, {}];

        for (let i = 0; i < size; i++) {
            for (let j=0; j < size; j++) {
                var num = $('#puzzle_sudoku_' + i + '_' + j).html();
                num = num.replace(/\s*/g, "");
                if (num == '') {
                    return false;
                }
                const boxIndex = parseInt(i/3) * 3 + parseInt(j/3);
                if (row[i + '-' + num]
                    || col[j + '-' + num]
                    || (size == 9 && boxes[boxIndex + '-' + num])
                ) {
                    return false;
                }

                row[i + '-' + num] = true;
                col[j + '-' + num] = true;
                boxes[boxIndex + '-' + num] = true;
            }
        }
        return true;
    }

    function htmlEncode(html) {return $("<div>").text(html).html()};
    function htmIDecode(encodedHtml) {return $("<div>").html(encodedHtml).text();}

    $("#logout_btn").click(function() {
        var unityVersion = $('#unity_version').val();
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/user/logout',
            data:{},
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //新消息获取成功
                if(obj["code"]==200){
                    location.href='/passport/web_login?unity_version=' + unityVersion;
                }
                //新消息获取失败
                else{
                    alert(obj.msg)
                }

            }
        });

    });

    $("#delete_btn").click(function() {
        var unityVersion = $('#unity_version').val();
        if (!window.confirm('您确认注销您的账号吗？注销以后，数据将全部丢失！')) {
            return false;
        }
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/user/delete',
            data:{},
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                console.log(obj);
                
                //新消息获取成功
                if(obj["code"]==200){
                    location.href='/passport/web_login?unity_version' + unityVersion;
                }
                //新消息获取失败
                else{
                    alert(obj.msg);
                    location.href='/passport/web_login?unity_version' + unityVersion;
                }

            }
        });

    });

    var oldI = -1;
    var tarI = -1;
    $('.puzzle_item').click(function() {
        // console.log($(this).attr('i'));
        var thisI = $(this).attr('i');

        if (oldI == '-1') {
            oldI = thisI;
            $(this).removeClass('puzzle_item');
            $(this).addClass('puzzle_item_active');
            console.log(oldI);
        } else {
            var oldDiv = $('#puzzle_image_' + oldI);
            var newDiv = $('#puzzle_image_' + thisI);

            oldDiv.removeClass('puzzle_item_active');
            oldDiv.addClass('puzzle_item');

            var tempDiv = newDiv.html();
            var tempDivId = newDiv.attr('id');
            var tempDivI = newDiv.attr('i');
            newDiv.html(oldDiv.html());
            newDiv.attr('id', oldDiv.attr('id'));
            newDiv.attr('i', oldDiv.attr('i'));
            oldDiv.html(tempDiv);
            oldDiv.attr('id', tempDivId);
            oldDiv.attr('i', tempDivI);
            oldI = -1;
            // var oldImg = $('#puzzle_image_'+oldI).attr('src');
            // var
            //     // .find('img').attr('src');
            // // console.log(newImg);
            // var tempI = thisI;
            // var newI = oldI;
            // oldI = tempI;

            // $(this).removeClass('puzzle_item_active');
            // $(this).addClass('puzzle_item');


            var items = $('.puzzle_check');
            var ct = 0;
            var right = 1;
            items.each(function () {
                var eachI = $(this).attr('i');
                console.log('eachI:' + eachI);
                console.log('ct:' + ct);
                if (eachI != ct) {
                    right = 0;
                    return false;
                }
                ct++;
            });
            console.log('right = ' + right);
            if (right == 1) {
                console.log(right);
                var user_id=$("input[name='user_id']").val();
                var session_id=$("input[name='session_id']").val();
                var session_stage_id=$("input[name='session_stage_id']").val();
                var qa_id=$("input[name='qa_id']").val();
                var story_id=$("input[name='story_id']").val();
                var begin_ts=$("input[name='begin_ts']").val();

                $.ajax({
                    type: "GET", //用POST方式传输
                    dataType: "json", //数据格式:JSON
                    async: false,
                    url: '/qa/add_user_answer',
                    data:{
                        user_id:user_id,
                        qa_id:qa_id,
                        answer:'True',
                        story_id:story_id,
                        session_id:session_id,
                        session_stage_id:session_stage_id,
                        begin_ts:begin_ts
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
                                $('.puzzle_check').unbind('click');
                                $('.puzzle_check').removeClass('puzzle_item');
                                $('.puzzle_check').addClass('puzzle_item_end');
                            if (obj.data.score.score != undefined) {
                                var score_text = "+" + obj.data.score.score + "枚";
                                if (obj.data.score.addition > 0) {
                                    score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                                }
                                $("#gold_score").html(score_text);
                            }
                            // var audio_right=$("#audio_right")[0];
                            // audo_right.play();
                                $("#answer-box").removeClass('hide');
                                $("#answer-right-box").removeClass('hide');

                                // $("#h5-right").modal('show');
                                // setTimeout(function (){
                                //     // Unity.call('WebViewOff&TrueAnswer');
                                //     var params = {
                                //         'WebViewOff':1,
                                //         'AnswerType':1
                                //     }
                                //     var data=$.toJSON(params);
                                //     Unity.call(data);
                                // },3000)
                                setTimeout(function () {
                                    // Unity.call('WebViewOff&TrueAnswer');
                                    // var params = {
                                    //     'WebViewOff':1,
                                    //     'AnswerType':1
                                    // }
                                    // var data=$.toJSON(params);
                                    // Unity.call(data);
                                    $("#answer-right-box").addClass('hide');
                                }, 4000);
                        }
                        //新消息获取失败
                        else{
                            $.alert(obj.msg)
                        }

                    }
                });

            }
        }
        // for (itemI in items) {
        //     // if (items[i].attr('i')) {
        //     //     tarI = i;
        //     // }
        //     console.log(itemI);
        //     // console.log($(i).attr('i'));
        // }
    });

    $('.puzzle_word_item').click(function() {
        // console.log($(this).attr('i'));
        // var thisI = $(this).attr('i');

        $(this).toggleClass('puzzle_word_item_active');

        // $(this).removeClass('puzzle_word_item');
        // $(this).addClass('puzzle_word_item_active');

        var st_answer = $('#st_answer').val();
        var ct = 0;
        var right = 0;
        var st_right = st_answer.length;
        var items = $('.puzzle_word_item_active');
        var ret = 0;
        items.each(function() {
            var eachVal = $(this).attr('val');
            if (eachVal == 0) {
                right = 0;
                return false;
            }
            ret += parseInt(eachVal);
            console.log(ret);
            if (ret == st_right) {
                right = 1;
            }
        });
        console.log('right = ' + right);
        if (right == 1) {
            console.log(right);
            var user_id=$("input[name='user_id']").val();
            var session_id=$("input[name='session_id']").val();
            var session_stage_id=$("input[name='session_stage_id']").val();
            var qa_id=$("input[name='qa_id']").val();
            var story_id=$("input[name='story_id']").val();
            var begin_ts=$("input[name='begin_ts']").val();

            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/qa/add_user_answer',
                data:{
                    user_id:user_id,
                    qa_id:qa_id,
                    answer:st_answer,
                    story_id:story_id,
                    session_id:session_id,
                    session_stage_id:session_stage_id,
                    begin_ts:begin_ts
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
                        $('.puzzle_item').unbind('click');
                        if (obj.data.score.score != undefined) {
                            var score_text = "+" + obj.data.score.score + "枚";
                            if (obj.data.score.addition > 0) {
                                score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                            }
                            $("#gold_score").html(score_text);
                        }
                        $("#answer-box").removeClass('hide');
                        $("#answer-right-box").removeClass('hide');
                        // var audio_right=$("#audio_right")[0];
                        // audo_right.play();
                        // $("#h5-right").modal('show');
                        setTimeout(function (){
                            // Unity.call('WebViewOff&TrueAnswer');
                            var params = {
                                'WebViewOff':1,
                                'AnswerType':1
                            }
                            var data=$.toJSON(params);
                            Unity.call(data);
                        },3000);
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });


            // setTimeout(function (){
            //     // Unity.call('WebViewOff&TrueAnswer');
            //     // var params = {
            //     //     'WebViewOff':1,
            //     //     'AnswerType':1
            //     // }
            //     // var data=$.toJSON(params);
            //     // Unity.call(data);
            //     $("#answer-right-box").addClass('hide');
            // }, 4000);
        }
        // for (itemI in items) {
        //     // if (items[i].attr('i')) {
        //     //     tarI = i;
        //     // }
        //     console.log(itemI);
        //     // console.log($(i).attr('i'));
        // }
    });

})