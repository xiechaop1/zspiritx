$(function () {
    var map = new AMap.Map('container', {
        resizeEnable: true,
        center: [116.397428, 39.90923],
        zoom: 13
    });

    map.clearMap();  // 清除地图覆盖物

    var markers = [{
        icon: '../../img/qa/btn_播放1_sel@2x.png',
        position: [116.205467, 39.907761]
    }, {
        icon: '../../img/qa/btn_播放1_sel@2x.png',
        position: [116.368904, 39.913423]
    }, {
        icon: '../../img/qa/btn_播放1_sel@2x.png',
        position: [116.305467, 39.807761]
    }];

    // 添加一些分布不均的点到地图上,地图上添加三个点标记，作为参照
    // drawPoi(markers)
    // markers.forEach(function(marker) {
    //     new AMap.Marker({
    //         map: map,
    //         icon: marker.icon,
    //         position: [marker.position[0], marker.position[1]],
    //         offset: new AMap.Pixel(-13, -30)
    //     });
    // });

    var center = map.getCenter();

    var centerText = '当前中心点坐标：' + center.getLng() + ',' + center.getLat();
    document.getElementById('centerCoord').innerHTML = centerText;
    document.getElementById('tips').innerHTML = '成功添加三个点标记，其中有两个在当前地图视野外！';

    // 添加事件监听, 使地图自适应显示到合适的范围
    AMap.event.addDomListener(document.getElementById('setFitView'), 'click', function() {
        var newCenter = map.setFitView();
        document.getElementById('centerCoord').innerHTML = '当前中心点坐标：' + newCenter.getCenter();
        document.getElementById('tips').innerHTML = '通过setFitView，地图自适应显示到合适的范围内,点标记已全部显示在视野中！';
    });
    function getPoi(){
        var user_id=$("input[name='user_id']").val();
        var session_id=$("input[name='session_id']").val();
        var user_lng=$("input[name='user_lng']").val();
        var user_lat=$("input[name='user_lat']").val();
        var dis_range=$("input[name='dis_range']").val();

        var user_id=1;
        var session_id=5;
        var user_lng=118.3726;
        var user_lat=39.3442;
        var dis_range=1000;
        var img_url='../../img/qa/btn_播放1_sel@2x.png';
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
                            iconPath: url,
                            id: obj.data[i].id || 0,
                            name: obj.data[i].user_id || '',
                            latitude: obj.data[i].lat,
                            longitude: obj.data[i].lng,
                            width: 25,
                            height: 48
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
    };

    function removeMarkers(){
        map.remove(markers);
    }

    function drawPoi(markers){
        markers.forEach(function(marker) {
            new AMap.Marker({
                map: map,
                icon: marker.icon,
                position: [marker.position[0], marker.position[1]],
                offset: new AMap.Pixel(-13, -30)
            });
        });
    };

    $(document).ready(function() {
        setInterval(getPoi(),1000)

    });
})