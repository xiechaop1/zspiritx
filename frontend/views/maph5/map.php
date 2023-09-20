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
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="team_id" value="<?= $teamId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="story_stage_id" value="<?= $storyStageId ?>">
<input type="hidden" name="user_lng" value="<?= $userLng ?>">
<input type="hidden" name="user_lat" value="<?= $userLat ?>">
<input type="hidden" name="dis_range" value="<?= $disRange ?>">



<div id="container"></div>

<!--<div class="input-card">
  <h4>地图自适应</h4>
  <input id="setFitView" type="button" class="btn" value="地图自适应显示" />
</div>
<div class="info">
  <div id="centerCoord"></div>
  <div id="tips"></div>
</div>-->
<div class="map-info-box" id="map-info-box">
  <img decoding="async"  src="../../static/img/map/x.png" alt="First slide" class="map-info-close">
  <div class="map-text-context">

  </div>
</div>
<div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
    返回
</div>

<script type="text/javascript">
        window._AMapSecurityConfig = {
            serviceHost:'https://h5.zspiritx.com.cn/_AMapService',
        }
</script>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=2.0&key=af1d4bafe8f99e4c53e02ba0eef6087c"></script>






