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
    });

    $("#return_btn").click(function (){
        if ($("#bgm")[0] != undefined && $("#bgm")[0].paused == false)
        {
            $("#bgm")[0].pause();
        }
        var params = {
            'WebViewOff':1
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    //页面重新加载
    $(".window-reload").on('click',function () {
        window.location.reload()
    });

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

    $(".shop_buy_pay_btn").click(function () {
        buyWareByPay($(this));
    });

    function buyWareByPay(thisObj) {
        var that=$("#answer-info");
        var story_id=$("input[name='story_id']").val();
        var user_id=$("input[name='user_id']").val();
        var session_id=$("input[name='session_id']").val();
        var shop_ware_id=$(thisObj).attr('data-id');
        // var order_id = 0;
        // var session_stage_id=$("input[name='session_stage_id']").val();
        // var begin_ts=$("input[name='begin_ts']").val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/order/create',
            data:{
                user_id:user_id,
                story_id:story_id,
                session_id:session_id,
                item_id:shop_ware_id,
                item_type:2,
                exec_method:2,
                is_test:1
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

                console.log(obj["code"]);

                //新消息获取成功
                if(obj["code"]==200){
                    // alert(obj.data.pay_res.h5_url);
                    var order_id = obj.data.order.id;
                    var payResult = setInterval(getPayInfo(user_id,order_id),3000);

                    var form = document.createElement('form');
                    document.body.appendChild(form);
                    form.method = "post";
                    form.action = obj.data.pay_res.h5_url;
                    form.submit();
                    document.body.removeChild(form);

                    // console.log(obj.data);
                    /* var order_status = obj.data.order.order_status;
                     if (order_status != 0 && (order_status == 1 || order_status == 2)) {
                         alert('购买成功！');
                     } else {
                         // 执行支付唤醒
                         alert('如果您遇到支付问题，请您和18500041193联系！');
                     }*/
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });
    };

    function getPayInfo(userId,orderId,storyId,isDebug){
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/orderdata/get_order',
            data:{
                user_id:userId,
                order_id:orderId,
                is_test:1
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {// console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                // $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //新消息获取成功
                if(obj["code"]==200){
                    var order_status = obj.data.order.order_status;
                    if(order_status==1){
                        window.location.reload();
                        // clearInterval(payResult);
                    }
                    if(order_status==3){
                        // window.location.reload();
                        clearInterval(payResult);
                    }

                    if (order_status != 0 && (order_status == 1 || order_status == 2)) {
                            alert('购买成功！现在您可以去参加挑战答题等活动，您的课包已经生效！');
                    } else {
                        // 执行支付唤醒
                        alert('如果您遇到支付问题，请您和18500041193联系！');
                    }

                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg);
                }

            }
        });
    }

    $(".shop_buy_score_btn").click(function () {
        buyWare($(this));
    });

    function buyWare(thisObj) {
        var that=$("#answer-info");
        var story_id=$("input[name='story_id']").val();
        var user_id=$("input[name='user_id']").val();
        var session_id=$("input[name='session_id']").val();
        var shop_ware_id=$(thisObj).attr('data-id');
        // var session_stage_id=$("input[name='session_stage_id']").val();
        // var begin_ts=$("input[name='begin_ts']").val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/shop/buy',
            data:{
                user_id:user_id,
                story_id:story_id,
                session_id:session_id,
                shop_ware_id:shop_ware_id,
                is_test:1
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

                console.log(obj["code"]);

                //新消息获取成功
                if(obj["code"]==200){
                    var shop_ware_name = obj.data.ware_name;

                    var user_score_ret = obj.data.user_score.score;
                    console.log(user_score_ret);
                    var scoreObj = $('#user_score_ret');
                    scoreObj.hide();
                    scoreObj.html(user_score_ret);
                    scoreObj.fadeIn();

                }
                //新消息获取失败
                else{
                    $.alert(obj.msg);
                }

            }
        });
    };

    $(".owl-carousel .buy_btn").click(function() {
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
                    exec_method:2,
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
                        alert(obj.data.pay_res.h5_url);
                        var payResult = setInterval(getPayInfo(user_id,order_id),3000);
                        
                        var form = document.createElement('form');
                        document.body.appendChild(form);
                        form.method = "post";
                        form.action = obj.data.pay_res.h5_url;
                        form.submit();
                        document.body.removeChild(form);

                        // console.log(obj.data);
                       /* var order_status = obj.data.order.order_status;
                        if (order_status != 0 && (order_status == 1 || order_status == 2)) {
                            alert('购买成功！');
                        } else {
                            // 执行支付唤醒
                            alert('如果您遇到支付问题，请您和18500041193联系！');
                        }*/
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });

            return false;
        }

        // if (userId != 0) {
        //     if (unityVersion != "") {
        //         var params = {
        //             'WebViewOff': 1,
        //             'DebugInfo': isDebug,
        //             'UserId': userId,
        //             'StoryId': storyId
        //         }
        //         var data = $.toJSON(params);
        //         console.log(data);
        //         Unity.call(data);
        //     } else {
        //         alert('已经购买！');
        //     }
        // } else {
        //     $('#loginform').show();
        // }
    });

    // 查询支付结果
    function getPayInfo(userId,orderId,storyId,isDebug){
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/orderdata/get_order',
            data:{
                user_id:userId,
                order_id:orderId,
                is_test:1
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {// console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                // $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //新消息获取成功
                if(obj["code"]==200){
                    var order_status = obj.data.order.order_status;
                    if(order_status==1){
                        window.location.reload();
                        // clearInterval(payResult);
                    }
                    if(order_status==3){
                        // window.location.reload();
                        clearInterval(payResult);
                    }

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
                        alert('如果您遇到支付问题，请您和18500041193联系！');
                    }

                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg);
                }

            }
        });
    }



})