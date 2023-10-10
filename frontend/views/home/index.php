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
<!---->
<audio autoplay loop>
  <source src="<?= $voice ?>" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" id="user_id" name="user_id" value="<?= $userId ?>">

<div class="owl-carousel owl-theme" id="banner">
  <div class="item">
    <input type="hidden" name="isDebug" value="0">
    <input type="hidden" name="storyId" value="1">
    <!-- Unity.call('WebViewOff&StartARScene'); -->
    <img decoding="async"  src="<?= $image ?>" alt="First slide" class="img-w-100">
    <div class="text-content">
      <div class="text-bg">
      </div>
      <div class='p-l-40'>
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
  <div class="item">
       <!-- onclick="Unity.call('WebViewOff&StartARScene');" -->

    <input type="hidden" name="isDebug" value="1">
    <input type="hidden" name="storyId" value="1">

    <img decoding="async"  src="<?= $image ?>" alt="First slide" class="img-w-100">
    <div class="text-content">
      <div class="text-bg">
      </div>
      <div class='p-l-40'>
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
  <div class="item">
    <!-- onclick="Unity.call('WebViewOff&StartARScene');" -->

    <input type="hidden" name="isDebug" value="0">
    <input type="hidden" name="storyId" value="2">

    <img decoding="async"  src="<?= $image ?>" alt="First slide" class="img-w-100">
    <div class="text-content">
      <div class="text-bg">
      </div>
      <div class='p-l-40'>
        <div class="fs-30 bold w-100 text-FF">
          陶然亭文化之旅
        </div>
        <div class="fs-24  w-100 text-FF m-t-30">
          看看是谁来了，一位坠入梦境的旅人。<br>
          去寻找属于你的山海残卷吧~
        </div>
        <div class="btn-m-green m-t-30 float-right m-r-20">
          进入游戏
          <!--<img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
        </div>
      </div>
    </div>
  </div>
  <div class="item">
    <!-- onclick="Unity.call('WebViewOff&StartARScene');" -->

    <input type="hidden" name="isDebug" value="1">
    <input type="hidden" name="storyId" value="3">

    <img decoding="async"  src="<?= $image ?>" alt="First slide" class="img-w-100">
    <div class="text-content">
      <div class="text-bg">
      </div>
      <div class='p-l-40'>
        <div class="fs-30 bold w-100 text-FF">
          侏罗纪-时间裂痕
        </div>
        <div class="fs-24  w-100 text-FF m-t-30">
          在大峡谷内，忽然产生了一段奇妙的故事。<br>
          因为时间裂痕，一些上古恐龙穿越到了现代，因为看到大峡谷有他们的同类，于是他们就来到了这里。
          <br>
          他们回不去了，这里的环境也不适宜，他们也对人类有着威胁和敌意。<br>
          传说只要集齐七颗水晶，就可以让他们穿越回去并且修复裂痕而！<br>
          冒险家，需要你们的帮助，去收集这些水晶了
        </div>
        <div class="btn-m-green m-t-30 float-right m-r-20">
          进入游戏
          <!--<img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
        </div>
      </div>
    </div>
  </div>
  <div class="item">
    <!-- onclick="Unity.call('WebViewOff&StartARScene');" -->

    <input type="hidden" name="isDebug" value="1">
    <input type="text" name="storyId" value="4" style="color: white;">

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
                手机号：<input class="answer-border" type="text" name="mobile" value="" id="mobile" style="margin: 5px;" ><br>
                验证码：<input class="answer-border" type="text" name="verifycode" value="" id="verifycode" style="margin: 5px;" ><br>
                <input type="checkbox" style="z-index: 99999999; opacity: inherit; position: relative;" id="agreement" name="agreement"> <a href="https://zspiritx.oss-cn-beijing.aliyuncs.com/doc/zspiritx_useragreement.docx">用户协议</a>
                <input type="hidden" name="is_debug" id="login_is_debug" value="">
                <input type="hidden" name="story_id" id="login_story_id" value="">
                <div class="btn-m-green m-t-30 float-right m-r-20" id="login_btn">
                  进入游戏
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

