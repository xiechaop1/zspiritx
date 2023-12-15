
    //计算屏幕宽度 高度
    var pageWidth = window.innerWidth;
    var pageHeight = window.innerHeight;
    if (typeof pageWidth != "number") {
        if (document.compatMode == "CSS1Compat") {
            pageWidth = document.documentElement.clientWidth;
            pageHeight = document.documentElement.clientHeight;
        } else {
            pageWidth = document.body.clientWidth;
            pageHeight = document.body.clientHeight;
        }
    }
    var zoom = 1;
    //compass div 宽高
    var paperWidth = 600;
    var paperHeight = 600;
    var crLong = 260 * zoom;
    var crShort = 200 * zoom;
    var cdiff = paperHeight / 2 - crLong;
    var initX = (pageWidth - paperWidth) > 0 ? (pageWidth - paperWidth) / 2 : 0;
    var initY = (pageHeight - paperHeight) > 0 ? (pageHeight - paperHeight) / 2 : 0;
    document.getElementById("compass").style.marginTop = initY + "px";
    document.getElementById("compass").style.marginLeft = initX + "px";

    //创建画布
    var compassPaper = Raphael(initX, initY, paperWidth, paperHeight)
    //画园
    // compassPaper.circle(paperWidth / 2, paperHeight / 2, crLong).attr('fill', 'black');

    var cross = compassPaper.set()
    var crossStyle = {
        stroke: 'white',
        'stroke-width': 1
    }
    //指南针画十字
    var pathlineX = 'M' + (paperWidth / 2 - crShort / 2) + ' ' + (paperHeight / 2) + 'L' + (paperWidth / 2 + crShort / 2) + ' ' + (paperHeight / 2);
    var pathlineY = 'M' + (paperWidth / 2) + ' ' + (paperHeight / 2 - crShort / 2) + 'L' + (paperWidth / 2) + ' ' + (paperHeight / 2 + crShort / 2);
    var northline = 'M' + (paperWidth / 2) + ' ' + (paperHeight / 2 - crShort) + 'L' + (paperWidth / 2) + ' ' + (crLong - crShort);

    // cross.push(
    //     compassPaper.path(pathlineX).attr(crossStyle),
    //     compassPaper.path(pathlineY).attr(crossStyle)
    // )
    //指北线
    // var northBar = compassPaper.path(northline).attr({
    //     stroke: 'white',
    //     'stroke-width': 4
    // })
    var compass = compassPaper.set()
    var strokeWidth
    var billet
    var degText
    for (var i = 0; i < 360; i = i + 5) {
        if (i % 30 == 0) {
           /* strokeWidth = 2
            degText = compassPaper.text(paperWidth / 2, (paperHeight / 2 - crShort) * 4 / 5, i).attr({
                fill: 'white',
                'font-size': '24rem'
            }).transform('R' + i + ', ' + paperWidth / 2 + ', ' + paperHeight / 2)
            degText.degPosition = i
            compass.push(degText)*/
        } else {
            strokeWidth = 1
        }
        billet = compassPaper.path('M' + paperWidth / 2 + ' ' + (paperHeight / 2 - crShort) + 'L' + paperWidth / 2 + '  ' + (paperHeight / 2 - crShort + crShort / 5)).attr({
            stroke: 'white',
            'stroke-width': strokeWidth
        }).transform('R' + i + ',' + paperWidth / 2 + ', ' + paperHeight / 2)
        billet.degPosition = i
        compass.push(
            billet
        );
    }
    ['北', '东', '南', '西'].forEach(function(direction, index) {
        var directionText = compassPaper.text(paperWidth / 2, (paperHeight / 8 - crShort/2 + crShort / 3), direction).attr({
            fill: 'white',
            'font-size': '40rem'
        }).transform('R' + index * 90 + ', ' + (paperWidth / 2) + ',' + paperHeight / 2)
        directionText.degPosition = index * 90
        compass.push(directionText)
    })

    var redTriLine = 'M' + (paperWidth / 2) + ' ' + ((paperHeight / 2 - crLong) + cdiff / 2) + ' L' + (paperWidth / 2 - (paperHeight / 2 - crShort) / 4) + ' ' + (paperHeight / 2 - crShort) + ' L' + (paperWidth / 2 + (paperHeight / 2 - crShort) / 4) + ' ' + (paperHeight / 2 - crShort) + 'Z';

    redTriLine = compassPaper.image("../../img/pin.png", paperWidth / 2-100, (paperHeight / 2 - crLong-80) + cdiff / 2, 200, 200);

    var redTriangle = compassPaper.path(redTriLine).attr({
        fill: 'red',
        'stroke-width': 0
    })
    redTriangle.degPosition = 0
    compass.push(redTriangle)

    var alphaText = compassPaper.text((paperWidth / 2), 880, '0°').attr({
        fill: 'white',
        'font-size': '30rem'
    })

    function throttle(method, delay, duration) {
        var timer = null,
            begin = new Date();
        return function() {
            var context = this,
                args = arguments,
                current = new Date();;
            clearTimeout(timer);
            if (current - begin >= duration) {
                method.apply(context, args);
                begin = current;
            } else {
                timer = setTimeout(function() {
                    method.apply(context, args);
                }, delay);
            }
        }
    }

    function deviceOrientationListener(event) {

        var alpha = event.webkitCompassHeading || event.alpha;

        $("#compass-motion").empty().text("event:"+event+", alpha:"+alpha);

        alphaText.attr({
            text: parseInt(alpha) + '°'
        });
        var directionIndex
        if (alpha > 337.5 || alpha < 22.5) {
            directionIndex = 0
        } else if (alpha > 45 - 22.5 && alpha < 45 + 22.5) {
            directionIndex = 1
        } else if (alpha > 90 - 22.5 && alpha < 90 + 22.5) {
            directionIndex = 2
        } else if (alpha > 135 - 22.5 && alpha < 135 + 22.5) {
            directionIndex = 3
        } else if (alpha > 180 - 22.5 && alpha < 180 + 22.5) {
            directionIndex = 4
        } else if (alpha > 225 - 22.5 && alpha < 225 + 22.5) {
            directionIndex = 5
        } else if (alpha > 270 - 22.5 && alpha < 270 + 22.5) {
            directionIndex = 6
        } else if (alpha > 315 - 22.5 && alpha < 315 + 22.5) {
            directionIndex = 7
        }
        compass.forEach(function(item) {
            item.transform('R' + (item.degPosition - alpha) + ',' + (paperWidth / 2) + ', ' + paperHeight / 2)
        })
    }

    // 是否是iOS手机
    function getIos() {
        var u = window.navigator.userAgent;
        return !! u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
    }

    function requestPermissionsIOS() {
        requestDeviceMotionIOS();
        requestDeviceOrientationIOS();
    }

    
    function requestDeviceMotionIOS() {

        if (typeof(DeviceMotionEvent).requestPermission === 'function') {
            (DeviceMotionEvent).requestPermission().then(permissionState =>{

                if (permissionState === 'granted') {
                    window.addEventListener('devicemotion', () =>{

                    });
                }
            }).
            catch((err) =>{
                alert(JSON.stringify(err))
                alert("用户未允许权限");
            })
        } else {
            // handle regular non iOS 13+ devices
        }
    }

    // requesting device orientation permission
    function requestDeviceOrientationIOS() {

        if (typeof(DeviceOrientationEvent).requestPermission === 'function') { (DeviceOrientationEvent).requestPermission().then(permissionState =>{
            if (permissionState === 'granted') {
                window.addEventListener('deviceorientation', () =>{

                });
            }
        }).
        catch((err) =>{
            alert(JSON.stringify(err))
            alert("用户未允许权限");
        })
        } else {
            // handle regular non iOS 13+ devices
        }
    }

    function addPermission() {
        requestPermissionsIOS();
    }

    //手机是否支持重力事件
    if (window.DeviceOrientationEvent) {
        window.addEventListener('deviceorientation', throttle(deviceOrientationListener, 10, 10))
        // alert(" support Device Orientation");

    } else {
        alert("Sorry your browser doesn't support Device Orientation");
    }


    $(function (){
        var winH=$(window).height()-260;
        // alert(winW);
        $(".compass-text").css('margin-left','-10px');

        $(".compass-text").css('margin-top',winH/2);
        $(".compass-text").removeClass('hide')
        //初始化距离信息
        var user_lng=$("input[name='user_lng']").val();
        var user_lat=$("input[name='user_lat']").val();
        var target_lng=$("input[name='user_lng']").val();
        var target_lat=$("input[name='user_lat']").val();

        var lnglat1 = new AMap.LngLat(target_lng, target_lat);
        var lnglat2 = new AMap.LngLat(user_lng, user_lat);
        var distance = lnglat1.distance(lnglat2);//计算lnglat1到lnglat2之间的实际距离(m)
        distance=Math.round(distance)
        $(".compass-text .color-red").empty().text(distance+" 米");



        //更具用户经纬度获取和目的地的信息
        function getDistance(){
            var user_id=$("input[name='user_id']").val();
            var target_lng=$("input[name='user_lng']").val();
            var target_lat=$("input[name='user_lat']").val();
            var lnglat1 = new AMap.LngLat(target_lng, target_lat);

            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: 'https://h5.zspiritx.com.cn/user/get_user_loc',
                data:{
                    user_id:user_id,
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
                        var lat=obj.data.lat;
                        var lng=obj.data.lng;

                        if(lat!=0&&lat!=null&&lat!=undefined&&lng!=0&&lng!=null&&lng!=undefined){
                            var lnglat2 = new AMap.LngLat(lng, lat);
                            var distance = lnglat1.distance(lnglat2);//计算lnglat1到lnglat2之间的实际距离(m)
                            distance=Math.round(distance)
                            $(".compass-text .color-red").empty().text(distance+"米");
                        }

                    }
                    //新消息获取失败
                    else{
                        // $.alert(obj.msg)
                    }

                }
            });






        }

        //定时更新距离信息
        setInterval(getDistance,1000);

    })