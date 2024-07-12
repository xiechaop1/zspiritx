$(function () {
  // 匹配页面倒计时计算
  var countdown=$("input[name='countdown']").val();
  var intervalCountDown=setInterval(count, 1000);

  function count() {
    console.log("countdown:",countdown)
    if (countdown > 0) {
      countdown--;
      $(".match-text-tag-1").text(countdown);
    }
    if (countdown==0) {
      clearInterval(intervalCountDown);
      $(".start-race-disable").addClass("hide");
      $(".start-race").removeClass('hide');
    }
  }

  var user_id=$("input[name='user_id']").val();
  var story_id=$("input[name='story_id']").val();
  var session_id=$("input[name='session_id']").val();
  var session_stage_id=$("input[name='session_stage_id']").val();
  function matchUser() {
    $.ajax({
      type: "GET", //用POST方式传输
      dataType: "json", //数据格式:JSON
      async: false,
      url: 'XXXXX',
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
          $(".header-m").attr('src','XXX')
        }
        //新消息获取失败
        else{
          $.alert(obj.msg)
        }

      }
    });

  }

})