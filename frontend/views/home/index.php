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


\frontend\assets\Qah5Asset::register($this);


$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = 'AR剧本杀';

?>
<audio autoplay loop>
  <source src="<?= $voice ?>" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<!-- <div class="w-100 m-auto">

<a href=" " onclick="Unity.call('WebViewOff&StartARScene');"><img src="<?= $image ?>" width="450" height="600"></a >
</div>-->

<div id="myCarousel" class="carousel slide">
  <!-- 轮播（Carousel）指标 -->
<!--  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
  </ol>-->
  <!-- 轮播（Carousel）项目 -->
  <div class="carousel-inner">
    <div class="item active" onclick="Unity.call('WebViewOff&StartARScene');">
      <img decoding="async"  src="<?= $image ?>" alt="First slide" class="img-w-100">
      <div class="text-content">
        <div class="fs-30 bold w-100 text-FF">
          标题：小猫有100块钱，
        </div>
        <div class="fs-24  w-100 text-FF m-t-30">
          内容：适应显示多个点标记 html, body, container { height: 100%; width: 100%; } .amap-icon img{ width: 25px; height: 34px; } 地图自适
        </div>
        <div class="btn-m-green m-t-30 float-right m-r-20">
          查看游戏
          <!--<img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
        </div>
      </div>
    </div>

  </div>
</div>

