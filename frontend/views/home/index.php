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
<style>
  .text-line-through {
    text-decoration: line-through;
    color: grey;
  }
</style>
<audio autoplay loop>
  <source src="<?= $voice ?>" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" id="user_id" name="user_id" value="<?= $userId ?>">

<div class="owl-carousel owl-theme" id="banner">
  <?php

  if (!empty($stories)) {
    foreach ($stories as $story) {
      $orderStatus = !empty($ordersMap[$story->id]) ? $ordersMap[$story->id] : \common\models\Order::ORDER_STATUS_WAIT;
  ?>
  <div class="item">
    <!-- onclick="Unity.call('WebViewOff&StartARScene');" -->

    <input type="hidden" name="isDebug" value="<?= $story->is_debug ?>">
    <input type="hidden" name="storyId" value="<?= $story->id ?>">
    <input type="hidden" name="orderStatus" value="<?= !empty($ordersMap[$story->id]) ? $ordersMap[$story->id] : 0 ?>">

    <img decoding="async"  src="<?= \common\helpers\Attachment::completeUrl($story->cover_image) ?>" alt="First slide" class="img-w-100">
    <div class="text-content">
      <div class="text-bg">
      </div>
      <div class='p-l-40'>
        <div class="fs-30 bold w-100 text-FF">
          <?= $story->title ?>
<!--          侏罗纪-时间裂痕-->
        </div>
        <div class="fs-24  w-100 text-FF m-t-30">
          <?= $story->desc ?>
          <!-- <span>价格：<span style="color: red; font-size: 24px; font-weight: bold;">限免</span></span>&nbsp; <span>位置：凯德茂·大峡谷</span><br>
          在大峡谷内，忽然产生了一段奇妙的故事。<br>
          因为时间裂痕，一些上古恐龙穿越到了现代，因为看到大峡谷有他们的同类，于是他们就来到了这里。
          <br>
          他们回不去了，这里的环境也不适宜，他们也对人类有着威胁和敌意。<br>
          传说只要集齐5颗宝石，就可以让他们穿越回去并且修复裂痕！ 冒险家，需要你们的帮助，去收集这些宝石了 -->
        </div>
        <div class="btn-m-green m-t-30 float-right m-r-20">
          <?php
          if ($orderStatus == \common\models\Order::ORDER_STATUS_PAIED) {
            echo '进入游戏';
          } else {
            if ($story->extend->curr_price == 0) {
              echo '限免';
            } else {
              if ($story->extend->curr_price != $story->extend->price) {
                echo '￥' . $story->extend->curr_price . ' <span class="text-line-through">（￥' . $story->extend->price . '）</span>';
              } else {
                echo '￥' . $story->extend->price;
              }
            }
          }

          ?>
          <!--<img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
        </div>
      </div>
    </div>
  </div>
  <?php

    }
  }
  ?>
<!--  <div class="item">-->
<!--    !-- onclick="Unity.call('WebViewOff&StartARScene');" -->
<!---->
<!--    <input type="hidden" name="isDebug" value="1">-->
<!--    <input type="hidden" name="storyId" value="3">-->
<!---->
<!--    <img decoding="async"  src="--><?php //= $banner['zhuluoji'] ?><!--" alt="First slide" class="img-w-100">-->
<!--    <div class="text-content">-->
<!--      <div class="text-bg">-->
<!--      </div>-->
<!--      <div class='p-l-40'>-->
<!--        <div class="fs-30 bold w-100 text-FF">-->
<!--          侏罗纪-时间裂痕（场内测试）-->
<!--        </div>-->
<!--        <div class="fs-24  w-100 text-FF m-t-30">-->
<!--          <span>价格：<span style="color: red; font-size: 24px; font-weight: bold;">限免</span></span>&nbsp; <span>位置：凯德茂·大峡谷</span><br>-->
<!--          在大峡谷内，忽然产生了一段奇妙的故事。<br>-->
<!--          因为时间裂痕，一些上古恐龙穿越到了现代，因为看到大峡谷有他们的同类，于是他们就来到了这里。-->
<!--          <br>-->
<!--          他们回不去了，这里的环境也不适宜，他们也对人类有着威胁和敌意。<br>-->
<!--          传说只要集齐七颗水晶，就可以让他们穿越回去并且修复裂痕而！<br>-->
<!--          冒险家，需要你们的帮助，去收集这些水晶了-->
<!--        </div>-->
<!--        <div class="btn-m-green m-t-30 float-right m-r-20">-->
<!--          进入游戏-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--  <div class="item">-->
<!---->
<!--    <input type="hidden" name="isDebug" value="0">-->
<!--    <input type="hidden" name="storyId" value="2">-->
<!---->
<!--    <img decoding="async"  src="--><?php //= $banner['taoranting'] ?><!--" alt="First slide" class="img-w-100">-->
<!--    <div class="text-content">-->
<!--      <div class="text-bg">-->
<!--      </div>-->
<!--      <div class='p-l-40'>-->
<!--        <div class="fs-30 bold w-100 text-FF">-->
<!--          陶然亭文化之旅-->
<!--        </div>-->
<!--        <div class="fs-24  w-100 text-FF m-t-30">-->
<!--          <span>价格：<span style="color: red; font-size: 24px; font-weight: bold;">限免</span></span>&nbsp; <span>位置：陶然亭</span><br>-->
<!--          看看是谁来了，一位坠入梦境的旅人。<br>-->
<!--          去寻找属于你的山海残卷吧~-->
<!--        </div>-->
<!--        <div class="btn-m-green m-t-30 float-right m-r-20">-->
<!--          进入游戏-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!---->
<!--  <div class="item">-->
<!--    <input type="hidden" name="isDebug" value="0">-->
<!--    <input type="hidden" name="storyId" value="1">-->
<!--    <img decoding="async"  src="--><?php //= $banner['senlin'] ?><!--" alt="First slide" class="img-w-100">-->
<!--    <div class="text-content">-->
<!--      <div class="text-bg">-->
<!--      </div>-->
<!--      <div class='p-l-40'>-->
<!--        <div class="fs-30 bold w-100 text-FF">-->
<!--          森林守护者-->
<!--        </div>-->
<!--        <div class="fs-24  w-100 text-FF m-t-30">-->
<!--          通过千⾟万苦，你踏进传说中的知识森林，感受到森林的神秘和宁静。这⾥的空⽓清新，阳光透过树叶投下斑驳的光影。<br>-->
<!--          你来此的⽬的正是为了寻找拥有渊博智慧的森林守护者，向他请教有关于保护环境的⽅法。根据位置提⽰去寻找他吧，在路上遇到的动物们会与你交流，引导你思考和学习。你不仅能得到宝贵的知识财富，说不定还能和他们成为朋友~-->
<!--        </div>-->
<!--        <div class="btn-m-green m-t-30 float-right m-r-20">-->
<!--          进入游戏-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--  <div class="item">-->
<!---->
<!--    <input type="hidden" name="isDebug" value="1">-->
<!--    <input type="hidden" name="storyId" value="1">-->
<!---->
<!--    <img decoding="async"  src="--><?php //= $banner['senlin'] ?><!--" alt="First slide" class="img-w-100">-->
<!--    <div class="text-content">-->
<!--      <div class="text-bg">-->
<!--      </div>-->
<!--      <div class='p-l-40'>-->
<!--        <div class="fs-30 bold w-100 text-FF">-->
<!--          森林守护者-->
<!--        </div>-->
<!--        <div class="fs-24  w-100 text-FF m-t-30">-->
<!--          通过千⾟万苦，你踏进传说中的知识森林，感受到森林的神秘和宁静。这⾥的空⽓清新，阳光透过树叶投下斑驳的光影。<br>-->
<!--          你来此的⽬的正是为了寻找拥有渊博智慧的森林守护者，向他请教有关于保护环境的⽅法。根据位置提⽰去寻找他吧，在路上遇到的动物们会与你交流，引导你思考和学习。你不仅能得到宝贵的知识财富，说不定还能和他们成为朋友~-->
<!--        </div>-->
<!--        <div class="btn-m-green m-t-30 float-right m-r-20">-->
<!--          进入游戏-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
  <div class="item">
    <!-- onclick="Unity.call('WebViewOff&StartARScene');" -->

    <input type="hidden" name="isDebug" value="1">

    <img decoding="async"  src="<?= $image ?>" alt="First slide" class="img-w-100">
    <div class="text-content">
      <div class="text-bg">
      </div>
      <div class='p-l-40'>
        <div class="fs-30 bold w-100 text-FF">
          侏罗纪-时间裂痕（家里测试）
        </div>
        <div class="fs-24  w-100 text-FF m-t-30">
          在大峡谷内，忽然产生了一段奇妙的故事。<br>
          因为时间裂痕，一些上古恐龙穿越到了现代，因为看到大峡谷有他们的同类，于是他们就来到了这里。
          <br>
          他们回不去了，这里的环境也不适宜，他们也对人类有着威胁和敌意。<br>
          传说只要集齐七颗水晶，就可以让他们穿越回去并且修复裂痕而！<br>
          冒险家，需要你们的帮助，去收集这些水晶了
        </div>
        SID: <input type="text" name="storyId" value="4" style="color: white; padding: 10px; font-size: 50px;">
        <div class="btn-m-green m-t-30 float-right m-r-20">
          进入游戏
          <!--<img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
        </div>
      </div>
    </div>
  </div>

</div>

<div id="loginform" class="w-100 m-auto" style="display: none; position: absolute; left: 0px; top: 50px; z-index: 99999999">

  <div class="p-20 bg-black">
    <div class="w-100 p-30  m-b-10">
      <div class="w-1-0 d-flex">
        <div class="fs-30 bold w-100 text-FF title-box-border">
          <div class="npc-name">
            注册 / 登录
          </div>

          <div class="row" id="answer-box">
            <div class="m-t-30 col-sm-12 col-md-12">
              <div class="answer-border">
                手机号：<br><input class="answer-border" type="text" name="mobile" value="" id="mobile" style="margin: 5px;" ><br>
                验证码：<br><input class="answer-border" type="text" name="verifycode" value="" id="verifycode" size="10" style="margin: 5px;" >&nbsp;<span><a href="javascript:void(0);" id="get_verifycode">获取验证码</a></span><br>
                  <input type="checkbox" style="z-index: 99999999; opacity: inherit; position: relative;" id="agreement" name="agreement"> <a href="https://zspiritx.oss-cn-beijing.aliyuncs.com/doc/zspiritx_useragreement.docx">用户协议</a>
                <input type="hidden" name="is_debug" id="login_is_debug" value="">
                <input type="hidden" name="story_id" id="login_story_id" value="">
                <div class="btn-m-green m-t-30 float-right m-r-20" id="login_btn">
                  进入游戏
                </div>
                <div class="btn-m-green m-t-30 float-right m-r-20" id="login_return_btn">
                  返回
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

</div>

