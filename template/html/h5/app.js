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

    //判断是否答对
    $("input[name='answer']").click(function () {
        submitAnswer($(this));
    });

    $("input[name='answer_txt']").change(function () {
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
        var v_ture=that.attr("data-value");
        var v_detail=that.attr("data-detail");
        if (qa_type == 1 || qa_type == 4) {
            var v_select = $("input[name='answer']:checked").val();
        } else if (qa_type == 7) {
            var v_select = $("input[name='answer_txt']").val();
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
                    session_stage_id:session_stage_id
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
                        if(v_ture==v_select){
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
                            },3000)
                        }
                        else{
                            $("#answer-box").hide();
                            $("#answer-error-box").removeClass('hide');
                            // $("#h5-worry").modal('show');
                            audio_wrong.play();
                            setTimeout(function (){
                                // Unity.call('WebViewOff&FalseAnswer');
                                var params = {
                                    'WebViewOff':1,
                                    'AnswerType':2
                                }
                                var data=$.toJSON(params);
                                Unity.call(data);
                            },3000)
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
                            Unity.call(params);
                        } else if (obj.data.type == 5) {
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
                                'WebViewOff': 1,
                            }
                            Unity.call(params);
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
                    location.href='/passport/web_login';
                }
                //新消息获取失败
                else{
                    alert(obj.msg)
                }

            }
        });

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
                        var order_status = obj.data.order_status;
                        if (order_status != 0 && order_status == 1) {
                            var params = {
                                'WebViewOff':1,
                                'DebugInfo':isDebug,
                                'UserId': userId,
                                'StoryId': storyId
                            }
                            var data=$.toJSON(params);
                            console.log(data);
                            Unity.call(data);
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
            var params = {
                'WebViewOff':1,
                'DebugInfo':isDebug,
                'UserId': userId,
                'StoryId': storyId
            }
            var data=$.toJSON(params);
            console.log(data);
            Unity.call(data);
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
                        location.href="/home/index";
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
        var params = {
            'WebViewOff':1,
            'AnswerType':2
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
        var knowledge_desc = obj.find("input[NAME='knowledge_content']").val();

        $('#knowledge_title').html(knowledge_title);
        $('#knowledge_image').html(knowledge_image);
        $('#knowledge_desc').html(knowledge_desc);

        $('#knowledge_detail').modal('show');
    }


    $("#logout_btn").click(function() {
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
                    location.href='/passport/web_login';
                }
                //新消息获取失败
                else{
                    alert(obj.msg)
                }

            }
        });

    });

    $("#delete_btn").click(function() {
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
                    location.href='/passport/web_login';
                }
                //新消息获取失败
                else{
                    alert(obj.msg);
                    location.href='/passport/web_login';
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
                        session_stage_id:session_stage_id
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
                    session_stage_id:session_stage_id
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
                        $("#answer-box").removeClass('hide');
                        $("#answer-right-box").removeClass('hide');
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