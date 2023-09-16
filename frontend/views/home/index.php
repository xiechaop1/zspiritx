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

<div id="myCarousel" class="carousel slide bg-333">
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
          森林守护者
        </div>
        <div class="fs-24  w-100 text-FF m-t-30">
          通过千⾟万苦，你踏进传说中的知识森林，感受到森林的神秘和宁静。这⾥的空⽓清新，阳光透过树叶投下斑驳的光影。<br>
          你来此的⽬的正是为了寻找拥有渊博智慧的森林守护者，向他请教有关于保护环境的⽅法。根据位置提⽰去寻找他吧，在路上遇到的动物们会与你交流，引导你思考和学习。你不仅能得到宝贵的知识财富，说不定还能和他们成为朋友~
        </div>
        <div class="btn-m-green m-t-30 float-right m-r-20">
          进入游戏
          <!--<img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
        </div>
      </div>
    </div>

  </div>
</div>

