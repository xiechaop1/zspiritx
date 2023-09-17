<?php
/**
 * Created by PhpStorm.
 * User: leeyifiei
 * Date: 2019/3/4
 * Time: 3:14 PM
 */

/**
 * @var \yii\web\View $this ;
 */

/**
 * @var \common\models\QA $qa
 */

\frontend\assets\maph5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

?>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<div id="container"></div>

<div class="input-card">
  <h4>地图自适应</h4>
  <input id="setFitView" type="button" class="btn" value="地图自适应显示" />
</div>
<div class="info">
  <div id="centerCoord"></div>
  <div id="tips"></div>
</div>


<script type="text/javascript">
        window._AMapSecurityConfig = {
            serviceHost:'https://h5.zspiritx.com.cn/_AMapService',
            // 例如 ：serviceHost:'http://1.1.1.1:80/_AMapService',
        }
</script>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.15&key=c86c9e45ba1226a8777f3e90dfceab3d"></script>
<script type="text/javascript">
    var position = new AMap.LngLat(116.397428, 39.90923);

    // 创建地图实例
    var map = new AMap.Map("container", {
        zoom: 13,
        center: position,
        resizeEnable: true
    });

    // 点标记显示内容，HTML要素字符串
    var markerContent = '' +
        '<div class="custom-content-marker">' +
        '   <img src="//a.amap.com/jsapi_demos/static/demo-center/icons/dir-via-marker.png">' +
        '   <div class="close-btn" onclick="clearMarker()">X</div>' +
        '</div>';

    var marker = new AMap.Marker({
        position: position,
        // 将 html 传给 content
        content: markerContent,
        // 以 icon 的 [center bottom] 为原点
        offset: new AMap.Pixel(-13, -30)
    });

    // 将 markers 添加到地图
    map.add(marker);

    // 清除 marker
    function clearMarker() {

        map.remove(marker);
    }
</script>

<!--
<script type="text/javascript" src="https://api.map.baidu.com/api?v=1.0&&type=webgl&ak=DcvMM0wWt8NZAQFcDmGsfeZiVqHEdaB2">
</script>
<script>
var map = new BMapGL.Map('container'); // 创建Map实例
map.centerAndZoom(new BMapGL.Point(116.404, 39.915), 12); // 初始化地图,设置中心点坐标和地图级别
map.enableScrollWheelZoom(true); // 开启鼠标滚轮缩放
</script>-->






