$(function () {

    $('#input_form').submit(function () {
        $.alert('您输入的用户名或者密码错误！');
       return false;
    });

    $('#reset_password').click(function() {
        if ($('#username').val().toUpperCase() != 'YUANDAWEI') {
            $.alert('您输入的用户名不存在！');
            return false;
        }

        var params = {
            'WebViewOff':1,
            'AnswerType':1
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

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
    });

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

})