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
     $(".answer-btn").on('click',function () {
        var that=$(this);
        var qa_id=that.attr("data-qa");
        var story_id=that.attr("data-story");
        var user_id=that.attr("data-user");
        var v_ture=that.attr("data-value");
        var v_detail=that.attr("data-detail");
        var v_select=$("input[name='answer']:checked").val();
        if(v_select==null){
            $("#h5-null").modal('show');
        }
        else if(v_ture==v_select){
          $("#h5-right").modal('show');
        }
        else{
          $("#h5-worry").modal('show');
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
                    story_id:story_id
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
                     

                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
    })

})