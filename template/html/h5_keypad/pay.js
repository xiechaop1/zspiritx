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

    function onBridgeReady() {
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
                console.log(`some error status = ${status.msg}`);
            }
        })
    };

    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
            document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
        }
    }else{
        onBridgeReady();
    }

    $('.pay').click(function () {
       onBridgeReady();
        // alert("拨打电话"+$keypadNum.text())
    });
})