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
<div class="w-100 m-auto">
<div id="container"></div>
<div class="input-card">
  <h4>地图自适应</h4>
  <input id="setFitView" type="button" class="btn" value="地图自适应显示" />
</div>
<div class="info">
  <div id="centerCoord"></div>
  <div id="tips"></div>
</div>
</div>
<!--
<script type="text/javascript">
        window._AMapSecurityConfig = {
            securityJsCode:'3d66e948d9c6ebc21ee6d90470ce405e',
        }
</script>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.15&key=c86c9e45ba1226a8777f3e90dfceab3d"></script>
-->
<script type="text/javascript" src="https://api.map.baidu.com/api?v=1.0&&type=webgl&ak=DcvMM0wWt8NZAQFcDmGsfeZiVqHEdaB2">
</script>







