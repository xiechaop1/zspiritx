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

<!--用户的ID，实时获取经纬度使用-->
<input type="hidden" name="user_id" value="<?= $userId ?>">

<!--用户的经纬度，可以为空-->
<input type="hidden" name="user_lng" value="<?= $userLng ?>">
<input type="hidden" name="user_lat" value="<?= $userLat ?>">

<!--目的地的经纬度-->
<input type="hidden" name="target_lng" value="<?= $userLng ?>">
<input type="hidden" name="target_lat" value="<?= $userLat ?>">


<!--目的地的经纬度-->
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="team_id" value="<?= $teamId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="story_stage_id" value="<?= $storyStageId ?>">
<input type="hidden" name="dis_range" value="<?= $disRange ?>">


<div class="btn-m-green m-t-30  m-l-30" id="return_btn">
    返回
</div>

<div class="compass-text">
  距离<span class="color-red">24.m</span>
</div>
<div id="compass"></div>

<script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.15&key=af1d4bafe8f99e4c53e02ba0eef6087c"></script>
<script src="//webapi.amap.com/ui/1.1/main.js?v=1.1.1"></script>



