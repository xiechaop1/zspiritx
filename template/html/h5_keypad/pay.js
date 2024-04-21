$(function () {
    <!--自适应布局-->
    (function () {
        var designW = 750;  //设计稿宽
        var font_rate = 100;
        //适配
        document.getElementsByTagName("html")[0].style.fontSize = document.body.offsetWidth / designW * font_rate + "px";
        document.getElementsByTagName("body")[0].style.fontSize = document.body.offsetWidth / designW * font_rate + "px";

        //监测窗口大小变化
        window.addEventListener("onorientationchange" in window ? "orientationchange" : "resize", function () {
            document.getElementsByTagName("html")[0].style.fontSize = document.body.offsetWidth / designW * font_rate + "px";
            document.getElementsByTagName("body")[0].style.fontSize = document.body.offsetWidth / designW * font_rate + "px";
        }, false);
    })();

    $("#return_btn").click(function (){
        var params = {
            'WebViewOff':1,
            'AnswerType':2
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $(".keypadinfo").slideDown(300);
    var $keypadNum = $("#keypadNum");
    $("#keypadNum").focus(function () {
        $(".keypadinfo").slideDown(300);
        document.activeElement.blur();
    });

    function onBridgeReady1() {
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/process/phone_call',
            data:{
                is_test:1,
                user_id:user_id,
                story_id:story_id,
                phone:phone
            },
            success(res) {
                var resdata = res.data;
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest', {
                        "appId": resdata.appId,          //公众号名称，由商户传入
                        "timeStamp": resdata.timeStamp,  //时间戳，自1970年以来的秒数
                        "nonceStr": resdata.nonceStr,   //随机串
                        "package": resdata.package,	  // 统一支付接口返回的prepay_id参数值
                        "signType": resdata.signType,  //微信签名方式：
                        "paySign": resdata.paySign 	//微信签名
                    },
                    function (res) {
                        if (res.err_msg == "get_brand_wcpay_request:ok") {
                            //res.err_msg将在用户支付成功后返回 ok，但并不保证它绝对可靠。
                            console.log('支付成功')
                        }
                        if (res.err_msg == "get_brand_wcpay_request:cancel") {
                            console.log('支付取消')
                        }
                        location.href = '成功或取消后跳转的页面';
                    });
            },
            error(status) {
                console.log('some error status = '+status.msg);
            }
        })
    };

    function onBridgeReady(){
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest', {
                "appId":"wx2421b1c4370ec43b",     //公众号ID，由商户传入
                "timeStamp":"1395712654",         //时间戳，自1970年以来的秒数
                "nonceStr":"e61463f8efa94090b1f366cccfbbb444", //随机串
                "package":"prepay_id=u802345jgfjsdfgsdg888",
                "signType":"MD5",         //微信签名方式：
                "paySign":"70EA570631E4BB79628FBCA90534C63FF7FADD89" //微信签名
            },
            function(res){
                if(res.err_msg == "get_brand_wcpay_request:ok" ){
                    // 使用以上方式判断前端返回,微信团队郑重提示：
                    //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                }
            });
    }


    var user_id = $("input[NAME='user_id']").val();
    var story_id = $("input[NAME='story_id']").val();
    var payResult;
    $('.pay,#pay-retry').click(function () {
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/order/create',
            data:{
                user_id:1,
                story_id:2,
                exec_method:2,
                is_test:1
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                // console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //新消息获取成功
                if(obj["code"]==200){
                    var order_id=obj.data.order.order_no;
                    $(".pay").hide();
                    $("#pay-retry,#pay-complete").show();

                    // window.open("obj.data.pay_res.h5_url");      //在另外新建窗口中打开窗口
                    var form = document.createElement('form');
                    document.body.appendChild(form);
                    form.method = "post";
                    form.action = obj.data.pay_res.h5_url;
                    form.submit();
                    document.body.removeChild(form);
                    
                    payResult= setInterval(getPayInfo(user_id,order_id),3000);

                }
                //新消息获取失败
                else{
                    alert(obj.msg);
                }

            }
        });
        // alert("微信支付");
    });



    function getPayInfo(userId,orderId){
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
                // alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //新消息获取成功
                if(obj["code"]==200){
                    var order_status=obj.data.order_status
                    if(order_status==1){
                        alert("支付成功",order_status);
                        clearInterval(payResult);

                    }
                    else if(order_status==3){
                        alert("支付成功",order_status);
                        clearInterval(payResult);

                    }

                }
                //新消息获取失败
                else{
                    // alert(obj.msg);
                }

            }
        });
    }


    $('#pay-complete').click(function () {
        alert("支付成功，页面跳转");
    });
})