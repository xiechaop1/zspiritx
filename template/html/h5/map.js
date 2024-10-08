var map = new AMap.Map('container', {
    mapStyle: 'amap://styles/dark',
    resizeEnable: true,
    zoom: 25
});
$(function () {
    var location=[];
    var markersUser = [];
    var markersTeam = [];
    var markersModal = [];

    $("#return_btn").click(function (){
        var params = {
            'WebViewOff': 1
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
            mapStyle: 'amap://styles/dark',
            resizeEnable: true,
            center: [user_lng, user_lat],
            zoom: 25
        });
    }
    else{
        var map = new AMap.Map('container', {
            mapStyle: 'amap://styles/dark',
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
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: 'https://h5.zspiritx.com.cn/user/get_user_loc',
            data:{
                user_id:user_id
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
                    else{
                        $.alert("定位失败")
                    }
                }
                //新消息获取失败
                else{
                     $.alert(obj.msg)
                }

            }
        });
    }


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

        getUserLocByTeam(user_id,session_id,user_lng,user_lat,dis_range);

        getSessionModels(user_id,story_id,session_id,user_lng,user_lat,story_stage_id,dis_range);
        getUserModels(user_id,story_id,session_id,user_lng,user_lat)
        getUserLoc(user_id)

    }

    //获取队伍成员位置信息
    function getUserLocByTeam(user_id,session_id,user_lng,user_lat,dis_range){
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
                    markersTeam = [];

                    for (var i in obj.data) {
                        if(obj.data[i].user_id!=user_id&&obj.data[i].lat!=null&&obj.data[i].lng!=null){
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
                            markersTeam.push(marker)
                        }
                    }

                    $(".marker_text").closest(".amap-marker").remove();
                    // removeMarkers();

                    drawPoi(markersTeam);
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });
    }

    //获取每个场景的模型
    function getSessionModels(user_id,story_id,session_id,user_lng,user_lat,story_stage_id,dis_range){
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

                //新消息获取成功
                if(obj["code"]==200){
                    markersModal = [];
                    for (var i in obj.data) {
                        if(obj.data[i].snapshot.lat!=null&&obj.data[i].snapshot.lng!=null){
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
                            markersModal.push(marker)
                        }
                    }
                    $(".marker_modal").closest(".amap-marker").remove();
                    drawModals(markersModal);
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });
    }

    //获取用户场景的模型
    function getUserModels(user_id,story_id,session_id,user_lng,user_lat){
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: 'https://h5.zspiritx.com.cn/process/get_user_model_loc',
            data:{
                user_id:user_id,
                story_id:story_id,
                session_id:session_id,
                user_lng:user_lng,
                user_lat:user_lat,
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

                //新消息获取成功
                if(obj["code"]==200){
                    markersModal = [];
                    var circle=[];
                    for (var i in obj.data) {
                        var e=obj.data[i][0];
                        if(e.location.lat!=null&&e.location.lng!=null
                            && userModelLocIds.includes(e.userModelLoc[0].id)==false
                            && e.userModelLoc[0].is_show == true
                        ){
                            var marker = {
                                iconPath: e.userModelLoc[0].storyModel.icon,
                                active_class:e.userModelLoc[0].active_class,
                                id: e.userModelLoc[0].id,
                                name: e.userModelLoc[0].storyModel.story_model_name,
                                location_name:e.location.location_name + e.location.id,
                                location_id:e.location.id,
                                latitude: e.location.lat,
                                longitude: e.location.lng,
                                width: 80,
                                height: 80,
                                img: e.userModelLoc[0].storyModel.icon,
                                title:2,
                                url:e.userModelLoc[0].link_url,
                                btn_text:e.userModelLoc[0].link_text,
                                loc_color:e.userModelLoc[0].loc_color,
                                amap_prop:e.location.amap_prop,
                                own_user_id:e.userModelLoc[0].user_id
                            };
                            markersModal.push(marker);
                            userModelLocIds.push(e.userModelLoc[0].id);

                            var amap_prop=e.location?.amap_prop;
                            if(amap_prop!=null&&amap_prop!=undefined){
                                // var n=$.toJSON(e.location.amap_prop);
                                // var c = eval( "(" + n + ")" );//转换后的JSON对象
                                var circleE=JSON.parse(e.location.amap_prop);
                                console.log("circle:",circleE)
                                var v=circleE.geofence?.circle;
                                if(v!=null&&v!=undefined) {
                                    circle.push(v);
                                }
                            }
                        }


                        console.log("circleArr:",circle)

                    }
                    $(".marker_modal").closest(".amap-marker").remove();
                    drawUserModals(markersModal);
                    drawCircle(circle);
                }
                //新消息获取失败
                else{
                    $.alert(obj.msg)
                }

            }
        });
    }


    //获取用户位置信息
    function getUserLoc(user_id){
        if (user_id==undefined) {
            user_id=$("input[name='user_id']").val();
        }
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: 'https://h5.zspiritx.com.cn/user/get_user_loc',
            data:{
                user_id:user_id
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象

                //新消息获取成功
                if(obj["code"]==200){
                    var lat=obj.data.lat;
                    var lng=obj.data.lng;

                    if(lat!=0&&lat!=null&&lat!=undefined&&lng!=0&&lng!=null&&lng!=undefined){
                        // map.setCenter([lng, lat]);
                        var marker = {
                            id: '',
                            name:'',
                            latitude: lat,
                            longitude: lng,
                            width: 80,
                            height: 80,
                            title:2
                        };
                        markersUser=marker
                        drawUser(markersUser);

                        // console.log("地图中心",lat,lng)
                    }

                }
                //新消息获取失败
                else{
                    // $.alert(obj.msg)
                }

            }
        });
    }

    //清楚地图上的Marker
    function removeMarkers(){
        map.clearMap();
    }

    //描绘Marker
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
    }

    //描绘模型Marker
    function drawModals(markers){
        markers.forEach(function(marker) {
            var markerContent= '<span style="left:20%;top:80%;"  class="marker_modal"  onclick="showPoiDetail('+marker.id+')" data-id="text id 1">' +
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
    }

    //描绘模型User Modal Marker
    function drawUserModals(markers){
        markers.forEach(function(marker) {
            var markerContent= '<span style="left:20%;top:80%;"  class="user_marker_modal user_marker_modal'+marker.active_class+'"  onclick="showPoiDetail(this)" data-type="'+marker.active_class+'" data-id="'+marker.id+'"  data-name="'+marker.name+'" ' +
                ' data-lat="'+marker.latitude+'"   data-lng="'+marker.longitude+'" data-own="'+marker.own_user_id+'"  data-url="'+marker.url+'"  data-btn="'+marker.btn_text+'" data-loc-id="'+marker.location_id+'" data-loc-name="'+marker.location_name+'">' +
                '<img src="'+marker.img+'">'+'</span>';
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
    }

    //描绘用户Marker
    var n=1;
    function drawUser(marker){
        $(".marker_user").closest(".amap-marker,.amap-markers").remove();
        $(".marker_user").remove();
        // n=n+1;
        // console.log("定位的经纬度",marker.longitude,marker.latitude)
        map.remove(markersUser);
        var markerContent= '<span style="left:20%;top:80%;"  class="marker_user"  onclick="" data-id="text id 1">' +
            '</span>';
        var marker= new AMap.Marker({
            content: markerContent,
            map: map,
            icon: marker.icon,
            position: [marker.longitude,marker.latitude],
            offset: new AMap.Pixel(-13, -30)
        });

    }

    //画圆
    function  drawCircle(markers){
        markers.forEach(function(marker) {
            if(marker!=null&&marker!=undefined){

                var radius=marker.radius/2;
                radius!=null&&radius!=undefined?'':radius=25;
                var borderWeight= marker.borderWeight;
                borderWeight!=null&&borderWeight!=undefined?'':borderWeight=3;
                var strokeColor=marker.strokeColor;
                strokeColor!=null&&strokeColor!=undefined?'':strokeColor="#FF33FF";
                var fillColor=marker.fillColor;
                fillColor!=null&&fillColor!=undefined?'':fillColor='#1791fc';
                var fillOpacity=marker.fillOpacity;
                fillOpacity!=null&&fillOpacity!=undefined?'':fillOpacity=0.3;
                var strokeStyle=marker.strokeStyle;
                strokeStyle!=null&&strokeStyle!=undefined?'':strokeStyle='dashed';
                var strokeWeight=marker.strokeWeight;
                strokeWeight!=null&& strokeWeight!=undefined?'': strokeWeight=1;
                var strokeOpacity=marker.strokeOpacity;
                strokeOpacity!=null&&strokeOpacity!=undefined?'':strokeOpacity=0.2;

                var circle = new AMap.Circle({
                    center:[marker.center.lng,marker.center.lat],
                    // center: [marker.lng,marker.lat],
                    radius: radius,
                    borderWeight: borderWeight,
                    strokeColor: strokeColor,
                    fillColor: fillColor,
                    fillOpacity:fillOpacity,
                    strokeStyle: strokeStyle,
                    strokeWeight: strokeWeight,
                    strokeOpacity: strokeOpacity,
                    strokeDasharray: [10, 10],
                    zIndex: 10
                  /*  radius: 25, //半径
                    borderWeight: 3,
                    strokeColor: "#FF33FF",
                    strokeWeight: 1,
                    strokeOpacity: 0.2,
                    fillOpacity: 0.3,
                    strokeStyle: 'dashed',
                    strokeDasharray: [10, 10],
                    // 线样式还支持 'dashed'
                    fillColor: '#1791fc',
                    zIndex: 10,*/
                })
                map.add(circle);
            }
        })

    }

    //绑定点击定位
    $("#user_center").click(function (){
        getUserPoi();
    })

    var userModelLocIds = new Array();

    getPoi();
    getUserLoc();
    getUserPoi();

    // setInterval(getPoi,10000);
    setInterval(getUserLoc,500);
    // setInterval(removeMarkers,3000);

    //打开url
    $(".btn-battle").click(function (){
        var me=$(this);
        var url=me.attr('data-url');

        // var target_stage_u_id = 'LJ-WORLD-OUTSIDE-' + me.attr('data-loc-id');
        // var params = {
        //     'naviModel': target_stage_u_id
        // }
        // var data=$.toJSON(params);
        // Unity.call(data);

        if(url.length>0){
            window.location.href=url
        }
        else{
            $.alert("数据异常，请刷新后重试");
        }
    });


    var startRotation = 30;
    var startPitch = 60;
    var startZoom = 12;
    var startCenter = [116.397217, 39.909071];

    var colors = [
        '#c57f34',
        '#cbfddf',
        '#edea70',
        '#8cc9f1',
        '#2c7bb6'
    ];

    // 10万辆北京公共交通车辆
   /* $.get('https://a.amap.com/Loca/static/mock/traffic_110000.csv', function (csv) {
        var layer = Loca.visualLayer({
            container: map,
            type: 'point',
            shape: 'circle'
        });

        layer.setData(csv, {
            lnglat: function (obj) {
                var value = obj.value;
                return [value['lng'], value['lat']];
            },
            type: 'csv'
        });

        layer.setOptions({
            style: {
                // 根据车辆类型设定不同半径
                radius: function (obj) {
                    var value = obj.value;
                    switch (parseInt(value.type)) {
                        case 3:
                            return 1;
                        case 4:
                            return 1.2;
                        case 41:
                            return 1.4;
                        case 5:
                            return 1.2;
                        default:
                            return 1;
                    }
                },
                // 根据车辆类型设定不同填充颜色
                color: function (obj) {
                    var value = obj.value;
                    switch (parseInt(value.type)) {
                        case 3:
                            return colors[0];
                        case 4:
                            return colors[1];
                        case 41:
                            return colors[2];
                        case 5:
                            return colors[3];
                        default:
                            return colors[4];
                    }
                },
                opacity: 0.8
            }
        });

        layer.render();
    });
*/

});

/*function getLocation(lat,lng){
    if(lat!=0&&lat!=null&&lat!=undefined&&lng!=0&&lng!=null&&lng!=undefined){
        map.setCenter([lng, lat]);
    }
    console.log("定时调用getLocation")

}
setTimeout(getLocation(39.3442,118.3726),1000);*/
function showPoiDetail(e) {
    var me=$(e);
    var user_id=$("input[name='user_id']").val();
    var type=me.attr("data-type");
    var name=me.attr("data-name");
    var location_name=me.attr("data-loc-name");
    var location_id=me.attr("data-loc-id");
    var id=me.attr("data-id");
    var url=me.attr("data-url");
    var btn=me.attr("data-btn");
    var target_lat=me.attr("data-lat");
    var target_lng=me.attr("data-lng");
    var own_user_id=me.attr("data-own");
    console.log(name,type,id,btn)
    // window.location.href="/compassh5/compass?user_id="+user_id+"&target_lat="+target_lat+"&target_lng="+target_lng
    if (own_user_id != user_id) {
        if (type == 2) {
            $("#modal-detail .map-text-context").empty().html(location_name + "<br>" + name);
            $("#modal-detail .btn-battle").attr("data-url", url);
            $("#modal-detail .btn-battle").attr("data-loc-id", location_id);
            $("#modal-detail .btn-battle").empty().text(btn);
            $("#modal-detail").modal('show');
        } else {
            var target_stage_u_id = 'LJ-WORLD-OUTSIDE-' + location_id;
            url = "/compassh5/compass?user_id=" + user_id + "&target_lat=" + target_lat + "&target_lng=" + target_lng;
            $("#modal-detail .map-text-context").empty().text(location_name);
            $("#modal-detail .btn-battle").attr("data-url", url);
            $("#modal-detail .btn-battle").attr("data-loc-id", location_id);
            $("#modal-detail .btn-battle").empty().text(btn);
            $("#modal-detail").modal('show');
        }
    }
    // else{
    //     var params = {
    //         'user_model_loc_id':id
    //     }
    //     var data=$.toJSON(params);
    //     Unity.call(data);
    //     window.location.href="/compassh5/compass?user_id="+user_id+"&target_lat="+target_lat+"&target_lng="+target_lng
    // }
}


