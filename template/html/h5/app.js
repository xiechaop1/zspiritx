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

    //判断是否答对
    $("input[name='answer']").change(function ()
    {
        var that=$("#answer-info");
        var qa_id=that.attr("data-qa");
        var story_id=that.attr("data-story");
        var user_id=$("input[name='user_id']").val();
        var session_id=$("input[name='session_id']").val();
        var v_ture=that.attr("data-value");
        var v_detail=that.attr("data-detail");
        var v_select=$("input[name='answer']:checked").val();
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
                    session_id:session_id
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
                        if(v_ture==v_select){
                            $("#answer-box").hide();
                            $("#answer-right-box").removeClass('hide');
                            // $("#h5-right").modal('show');
                            setTimeout(function (){
                                Unity.call('WebViewOff&TrueAnswer');
                            },3000)
                        }
                        else{
                            $("#answer-box").hide();
                            $("#answer-error-box").removeClass('hide');
                            // $("#h5-worry").modal('show');
                            setTimeout(function (){
                                Unity.call('WebViewOff&FalseAnswer');
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
    })

    $("input[name='baggage']").change(function ()
    {
        var that=$("#answer-info");
        var user_id=$("input[name='user_id']").val();
        var story_id=$("input[name='story_id']").val();
        var session_id=$("input[name='session_id']").val();
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
                        // if(v_ture==v_select){
                            $.alert('使用成功！');
                        setTimeout(function (){
                            window.location.reload();
                        },3000)

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


    $(".owl-carousel .item").click(function() {
        var t = $(this);
        var isDebug = t.find("input[name='isDebug']").val();
        var storyId = t.find("input[name='storyId']").val();
        $('#login_is_debug').val(isDebug);
        $('#login_story_id').val(storyId);

        var userId = $('#user_id').val();

        if (userId != 0) {
            var params = {
                'WebViewOff':1,
                'DebugInfo':isDebug,
                'UserId': userId,
                'StoryId': storyId
            }
            var data=$.toJSON(params);
            Unity.call(data);
        } else {
            $('#loginform').show();
        }
    });
    $("#login_btn").click(function ()
    {
        var mobile=$("input[name='mobile']").val();
        var isDebug = $('#login_is_debug').val();
        var storyId = $('#login_story_id').val();
        var userId = $('#user_id').val();
        var isagree = $('#agreement').val();

        if (mobile != '1' && isagree == false) {
            $.alert('请勾选用户协议');
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
                        var params = {
                            'WebViewOff':1,
                            'DebugInfo':isDebug,
                            'UserId': obj.data.id,
                            'StoryId': storyId
                        }
                        var data=$.toJSON(params);
                        // var data = eval( "{" + paramsjson + "}" );

                        // Unity.call('{'WebViewOff':1, 'DebugInfo':isDebug, 'UserId': obj.data.id, 'StoryId': storyId }');
                        Unity.call(data);
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
    });

    $("#return_btn").click(function (){
        var params = {
            'WebViewOff':1,
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $(".knowledge-title").click(function (){
        var tobj = $(this).parent().find(".knowledge-content");
        var allobj = $(".knowledge-content");

        if (tobj.is(':hidden')) {
            allobj.hide();
           tobj.show();
       } else {
           tobj.hide();
       }
        // $(".knowledge-content").hide();
    });

})