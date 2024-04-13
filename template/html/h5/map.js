var map = new AMap.Map('container', {
});
$(function () {
    var location=[];

    $("#return_btn").click(function (){
        var params = {
            'WebViewOff':1
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $(".map-info-close").on('click',function (){
        var me=$(this);
        $("#map-info-box").hide();
    });


    var user_lng=$("input[name='user_lng']").val();
    var user_lat=$("input[name='user_lat']").val();

    if(user_lng!=null&&user_lng!=undefined&&user_lng!=0&&user_lat!=null&&user_lat!=undefined&&user_lat!=0){
        var map = new AMap.Map('container', {
            resizeEnable: true,
            center: [user_lng, user_lat],
            zoom: 25
        });
    }
    else{
        var map = new AMap.Map('container', {
            resizeEnable: true,
            center: [116.397428, 39.90923],
            zoom: 25
        });

    }

    AMapUI.loadUI(['control/BasicControl'], function(BasicControl) {
        //缩放控件，显示Zoom值
        map.addControl(new BasicControl.Zoom({
            position: 'rb',
        }));

    });

    //定位按钮



    // map.clearMap();  // 清除地图覆盖物

    //markers 测试数据
    var markersExample = [{
        // icon: '../../img/map/marker_1.png',
        position: [116.205467, 39.907761]
    }, {
        // icon: '../../img/map/marker_1.png',
        position: [116.368904, 39.913423]
    }, {
        // icon: '../../img/map/marker_1.png',
        position: [116.305467, 39.807761]
    }];


    function  showPoiDetail(n){
        var me=$(this);
        var text=me.find('.marker_text').attr("data-id");
        text=me.attr("data-id");
        // text=mapEvent.target.dom.getElementsByClassName('marker_text')[0].getAttribute('data-id');
        $("#map-info-box").show();
        console.log(text,n,me)
        $("#map-info-box .map-text-context").text(n);
    }

    //以用户GPS为中心定位
    function getUserPoi(lng,lat){
        var user_id=$("input[name='user_id']").val();
        var story_id=$("input[name='story_id']").val();
        var session_id=$("input[name='session_id']").val();
        var user_lng=$("input[name='user_lng']").val();
        var user_lat=$("input[name='user_lat']").val();
        var dis_range=$("input[name='dis_range']").val();
        var story_stage_id=$("input[name='story_stage_id']").val();

        user_id!=null&&user_id!=undefined>0?'':user_id=1;
        story_id!=null&&story_id!=undefined>0?'':story_id=1;
        session_id!=null&&session_id!=undefined?'':session_id=5;
        dis_range!=null&&dis_range!=undefined&&dis_range!=0?'':dis_range=1000;
        story_stage_id!=null&&story_stage_id!=undefined&&story_stage_id!=0?'':story_stage_id=1;

        var center = map.getCenter();
        h5_lng=center.getLng();
        h5_lat=center.getLat();
        if(location[0]!=null&&location[0]!=undefined&&location[0]!=null&&location[0]!=undefined){
            user_lng=location[0];
            user_lat=location[1];
        }
        else if(user_lng!=null&&user_lng!=undefined&&user_lng!=0&&user_lat!=null&&user_lat!=undefined&&user_lat!=0){

        }
        else if(h5_lng!=null&&h5_lng!=undefined&&h5_lng!=0&&h5_lat!=null&&h5_lat!=undefined&&h5_lat!=0){
            user_lng=h5_lng;
            user_lat=h5_lat;
        }
        else{
            user_lng=118.3726;
            user_lat=39.3442;
        }

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
                        map.setCenter([lng, lat]);
                        console.log("地图中心",lat,lng)
                    }
                }
                //新消息获取失败
                else{
                     $.alert(obj.msg)
                }

            }
        });
    };

    //获取Poi 点详情
    function getPoi(lng,lat){
        var user_id=$("input[name='user_id']").val();
        var story_id=$("input[name='story_id']").val();
        var session_id=$("input[name='session_id']").val();
        var user_lng=$("input[name='user_lng']").val();
        var user_lat=$("input[name='user_lat']").val();
        var dis_range=$("input[name='dis_range']").val();
        var story_stage_id=$("input[name='story_stage_id']").val();

        user_id!=null&&user_id!=undefined>0?'':user_id=1;
        story_id!=null&&story_id!=undefined>0?'':story_id=1;
        session_id!=null&&session_id!=undefined?'':session_id=5;
        dis_range!=null&&dis_range!=undefined&&dis_range!=0?'':dis_range=1000;
        story_stage_id!=null&&story_stage_id!=undefined&&story_stage_id!=0?'':story_stage_id=1;


        var center = map.getCenter();
        h5_lng=center.getLng();
        h5_lat=center.getLat();
        if(location[0]!=null&&location[0]!=undefined&&location[0]!=null&&location[0]!=undefined){
            user_lng=location[0];
            user_lat=location[1];
        }
        else if(user_lng!=null&&user_lng!=undefined&&user_lng!=0&&user_lat!=null&&user_lat!=undefined&&user_lat!=0){

        }
        else if(h5_lng!=null&&h5_lng!=undefined&&h5_lng!=0&&h5_lat!=null&&h5_lat!=undefined&&h5_lat!=0){
            user_lng=h5_lng;
            user_lat=h5_lat;
        }
        else{
            user_lng=118.3726;
            user_lat=39.3442;
        }

        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: 'https://h5.zspiritx.com.cn/user/get_user_loc_by_team',
            data:{
                user_id:user_id,
                session_id:session_id,
                user_lng:user_lng,
                user_lat:user_lat,
                dis_range:dis_range
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
                    var markers = [];
                    for (var i in obj.data) {
                        var marker = {
                            // iconPath: url,
                            id: obj.data[i].id || 0,
                            name: obj.data[i].user_id || '',
                            latitude: obj.data[i].lat,
                            longitude: obj.data[i].lng,
                            width: 80,
                            height: 80,
                            title:1
                        };
                        markers.push(marker)
                    }
                    removeMarkers();

                    drawPoi(markers);
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });

        //获取每个场景的模型
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: 'https://h5.zspiritx.com.cn/process/get_session_models',
            data:{
                user_id:user_id,
                story_id:story_id,
                session_id:session_id,
                user_lng:user_lng,
                user_lat:user_lat,
                // dis_range:dis_range,
                // story_stage_id:story_stage_id,
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
                    var markers = [];
                    for (var i in obj.data) {
                        var marker = {
                            // iconPath: url,
                            id: obj.data[i].id || 0,
                            name: obj.data[i].user_id || '',
                            latitude: obj.data[i].snapshot.lat,
                            longitude: obj.data[i].snapshot.lng,
                            width: 80,
                            height: 80,
                            img: obj.data[i].snapshot.lng,
                            title:2
                        };
                        markers.push(marker)
                    }
                    // removeMarkers();

                    drawPoi(markers);



                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });

        //获取用户位置信息
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
                        // map.setCenter([lng, lat]);
                        var markerUser = {
                            // iconPath: url,
                            id: '',
                            name:'',
                            latitude: lat,
                            longitude: lng,
                            width: 80,
                            height: 80,
                            title:2
                        };
                        drawUser(markerUser);
                        // var markerUser = new AMap.Marker({
                        //     position: [lng, lat]   // 经纬度对象，也可以是经纬度构成的一维数组[116.39, 39.9]
                        // });
                       // 将创建的点标记添加到已有的地图实例：
                       //  map.add(markerUser);
                        console.log("地图中心",lat,lng)
                    }

                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg)
                }

            }
        });
    };

    function removeMarkers(){
        map.clearMap();
        // map.remove(markers);
    }

    function drawPoi(markers){
        markers.forEach(function(marker) {
            var markerContent= '<span style="left:20%;top:80%;"  class="marker_text"  onclick="showPoiDetail('+marker.id+')" data-id="text id 1">' +
                marker.title+'</span>';
            var marker= new AMap.Marker({
                content: markerContent,
                map: map,
                icon: marker.icon,
                position: [marker.longitude,marker.latitude],
                offset: new AMap.Pixel(-13, -30)
            });
            // marker.on('click', function(e){
            //     showPoiDetail(e);
            // });
        });
        // markers.forEach(function(marker) {
        //     var markerContent= '<span style="left:20%;top:80%;"  class="marker_text" data-id="'+marker.title+'">'+marker.title
        //         '</span>';
        //     var marker= new AMap.Marker({
        //         content: markerContent,
        //         map: map,
        //         // icon: marker.icon,
        //         position: [marker.longitude, marker.latitude],
        //         offset: new AMap.Pixel(-13, -30)
        //     });
        //     markers.on('click', function(e){
        //         showPoiDetail();
        //     });
        // });
    }

    function drawUser(marker){
        var markerContent= '<span style="left:20%;top:80%;"  class="marker_user"  onclick="showPoiDetail('+marker.id+')" data-id="text id 1">' +
            '</span>';
        var marker= new AMap.Marker({
            content: markerContent,
            map: map,
            icon: marker.icon,
            position: [marker.longitude,marker.latitude],
            offset: new AMap.Pixel(-13, -30)
        });
    }

    //绑定点击定位
    $("#user_center").click(function (){
        getUserPoi();
    })

    drawPoi(markersExample);
    getPoi();
    getUserPoi();

    setInterval(getPoi,60000);
    setInterval(getUserPoi,5000);
    // setInterval(removeMarkers,3000);
    
});

/*function getLocation(lat,lng){
    if(lat!=0&&lat!=null&&lat!=undefined&&lng!=0&&lng!=null&&lng!=undefined){
        map.setCenter([lng, lat]);
    }
    console.log("定时调用getLocation")

}
setTimeout(getLocation(39.3442,118.3726),1000);*/
function showPoiDetail(n) {
    var me=$(this);
    var text=me.find('.marker_text').attr("data-id");
    text=me.attr("data-id");
    text=$(this).attr('data-id');
    $("#map-info-box").show();
    console.log(text,me,n)
    $("#map-info-box .map-text-context").empty().text(n);

}


