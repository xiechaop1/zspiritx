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
<input type="hidden" name="session_id" value="<?= $userId ?>">
<input type="hidden" name="user_lng" value="<?= $userId ?>">
<input type="hidden" name="user_lat" value="<?= $userId ?>">
<input type="hidden" name="udis_range" value="<?= $userId ?>">


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
        }
</script>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=2.0&key=c86c9e45ba1226a8777f3e90dfceab3d"></script>






