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

\frontend\assets\Compassh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '罗盘';

?>
<style>
    body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
    }
    .compass {
        width: 200px;
        height: 200px;
        background: url('../../static/img/map/compass.png') no-repeat center;
        background-size: cover;
        position: relative;
    }
    .needle {
        width: 200px;
        height: 200px;
        background: url('../../static/img/map/needle.png') no-repeat center;
        background-size: cover;
        position: absolute;
        top: 0;
        left: 0;
        transform-origin: 50% 50%;
        transition: transform 0.5s ease-in-out;
    }
</style>

<!--用户的ID，实时获取经纬度使用-->
<input type="hidden" name="user_id" value="<?= $userId ?>">

<!--用户的经纬度，可以为空-->
<input type="hidden" name="user_lng" value="<?= $userLng ?>">
<input type="hidden" name="user_lat" value="<?= $userLat ?>">

<!--目的地的经纬度-->
<input type="hidden" name="target_lng" value="<?= $targetLng ?>">
<input type="hidden" name="target_lat" value="<?= $targetLat ?>">


<!--目的地的经纬度-->
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="team_id" value="<?= $teamId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="story_stage_id" value="<?= $storyStageId ?>">
<input type="hidden" name="dis_range" value="<?= $disRange ?>">


<div class="btn-m-green m-t-30  m-l-30 go-history" >
    返回
</div>

<div class="compass-text hide">
  距离 <span class="color-red">-- 米</span>
</div>
<div class="box-center m-t-40">
  <div class="btn-m-green m-t-30  m-auto"  onclick="addPermission()">
    开启罗盘
  </div>
</div>

<!--<div id="compass-motion">
</div>-->

<div class="compass">
    <div class="needle"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js"></script>

<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.15&key=af1d4bafe8f99e4c53e02ba0eef6087c"></script>
<script src="//webapi.amap.com/ui/1.1/main.js?v=1.1.1"></script>
<script>
    $(document).ready(function() {
        // Destination coordinates
        const destinationLat = 40.7128; // Example: New York latitude
        const destinationLng = -74.0060; // Example: New York longitude

        // Update compass direction based on device orientation and geolocation
        function updateCompass(event) {
            const alpha = event.alpha; // Device orientation angle
            navigator.geolocation.getCurrentPosition(position => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                // Calculate bearing between user and destination
                const bearing = calculateBearing(userLat, userLng, destinationLat, destinationLng);

                // Update needle rotation
                const needleRotation = bearing - alpha;
                $('.needle').css('transform', 'rotate(' + needleRotation + 'deg)');
            });
        }

        // Calculate bearing between two points
        function calculateBearing(lat1, lng1, lat2, lng2) {
            const dLon = (lng2 - lng1) * Math.PI / 180.0;
            const y = Math.sin(dLon) * Math.cos(lat2 * Math.PI / 180.0);
            const x = Math.cos(lat1 * Math.PI / 180.0) * Math.sin(lat2 * Math.PI / 180.0) -
                Math.sin(lat1 * Math.PI / 180.0) * Math.cos(lat2 * Math.PI / 180.0) * Math.cos(dLon);
            let bearing = Math.atan2(y, x) * 180.0 / Math.PI;
            bearing = (bearing + 360.0) % 360.0;
            return bearing;
        }

        // Request device orientation and geolocation permissions
        if (window.DeviceOrientationEvent) {
            window.addEventListener('deviceorientation', updateCompass, true);
        } else {
            alert('Device orientation not supported');
        }
    });
</script>


