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
</div>
<div class="map-info-box" id="map-info-box">
  <div class="map-info-content">
    <img decoding="async"  src="../../static/img/map/x.png" alt="First slide" class="map-info-close">
    <div class="map-text-context">
    </div>
  </div>

</div>-->

<div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
    返回
</div>
<div id="user_center">
  <img src="../../static/img/position.png">
</div>
<div class="compass_btn">
  <a href="/compassh5/compass">
    <img src="../../img/map/compass.png">
  </a>
</div>

<!-- 场景详情 -->
<div class="modal fade" id="modal-detail" tabindex="-1" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal">
         <img decoding="async"  src="../../img/map/x.png" alt="First slide" class="map-info-close">
      </span>
      <div class="p-20 relative h5 m-t-30" name="loginStr">
        <div>
          <div class="fs-15 text-33 bold">
           场景简介：
          </div>
          <div class="m-t-20 bg-F5 p-20 fs-14  border-radius-r-5 border-radius-l-5 map-text-context">
            场景信息XXXXX
          </div>
        </div>
      </div>

    </div>
  </div>
</div>


<script type="text/javascript">
        window._AMapSecurityConfig = {
            serviceHost:'https://h5.zspiritx.com.cn/_AMapService',
        }
</script>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.15&key=af1d4bafe8f99e4c53e02ba0eef6087c"></script>
<script src="//webapi.amap.com/ui/1.1/main.js?v=1.1.1"></script>






