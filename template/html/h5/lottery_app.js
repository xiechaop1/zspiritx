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

    $(".lottery-btn").click(function() {
        var that=$("#answer-info");
        var user_id=$("input[name='user_id']").val();
        var story_id=$("input[name='story_id']").val();
        var session_id=$("input[name='session_id']").val();
        var lottery_id=$("input[name='lottery_id']").val();
        var channel_id=$("input[name='channel_id']").val();
        var opt_ct=$("input[name='opt_ct']").val();
        var user_lottery_id=$("input[name='user_lottery_id']").val();

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/lottery/award',
            data:{
                user_id:user_id,
                story_id:story_id,
                session_id:session_id,
                lottery_id:lottery_id,
                channel_id:channel_id,
                user_lottery_id:user_lottery_id,
                opt_ct:opt_ct,
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

                    if (obj.data.isAward == 1) {
                        $('#lottery-success .lottery-title').empty().text(obj.data.finalPrize.prize_name);
                        $('#lottery-success .lottery-detail').empty().text(obj.data.msg);
                        $("#lottery-success .lottery-success-title").show();
                        $("#lottery-success .lottery-error-title").hide();

                        var dialog = $('#lottery-success');
                    } else {
                        $('#lottery-success .lottery-title').empty();
                        $('#lottery-success .lottery-detail').empty().text(obj.msg);
                        $("#lottery-success .lottery-success-title").hide();
                        $("#lottery-success .lottery-error-title").show();
                        var dialog = $('#lottery-success');
                    }
                    dialog.modal('show');
                    // dialog.show();


                        // var params = {
                        //     'WebViewOff': 1
                        // }
                        // var data=$.toJSON(params);
                        // Unity.call(data);


                }
                //新消息获取失败
                else{

                    $('#lottery-success .lottery-title').empty();
                    $('#lottery-success .lottery-detail').empty().text(obj.msg);
                    $("#lottery-success .lottery-success-title").hide();
                    $("#lottery-success .lottery-error-title").show();
                    var dialog = $('#lottery-success');
                    dialog.modal('show');
                    // $.alert(obj.msg)
                }

            }
        });
    });

})