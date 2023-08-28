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
        var v_ture=that.attr("data-value");
        var v_detail=that.attr("data-detail");
        var v_select=$("input[name='answer']:checked").val();
        if(v_select==null){
            alert("什么也没选中!");
        }
        else if(v_ture==v_select){
          $("#h5-right").modal('show');
        }
        else{
          $("#h5-worry").modal('show');
        }
    })

})