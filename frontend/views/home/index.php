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


\frontend\assets\Homeh5Asset::register($this);


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
  <source src="<?= $bgSound ?>" type="audio/mpeg">
  您的浏览器不支持 audio 元素。
</audio>
<input type="hidden" id="user_id" name="user_id" value="<?= $userId ?>">
<input type="hidden" id="unity_version" name="unity_version" value="<?= $unityVersion ?>">

<div style="position: absolute; z-index: 999; margin: 20px; color: white; font-size: 24px;">
  <?php
  if (empty($unityVersion)) {
  ?>
  <a href="/home/index"><img src="https://zspiritx.oss-cn-beijing.aliyuncs.com/img/home/logo_white_1024.png" style="width: 150px;"></a>
  <?php
  } else {
  ?>
  <img src="https://zspiritx.oss-cn-beijing.aliyuncs.com/img/home/icon_1024x1024.png" style="width: 50px;"> 灵镜
  <?php
  }
  ?>
</div>
<div class="owl-carousel owl-theme" id="banner">
  <?php
  if (
      !empty($user) &&
      $user->mobile >= 90 && $user->mobile <= 120 &&
      $user->user_type == \common\models\User::USER_TYPE_INNER) {
    ?>
    <div class="item">
      <!-- onclick="Unity.call('WebViewOff&StartARScene');" -->

      <input type="hidden" name="isDebug" value="0">
      <input type="hidden" name="storyId" value="4" style="color: white; padding: 10px; font-size: 50px;">
      <div class="btn-m-green m-t-30 float-right m-r-20" style="position: absolute; right: 0px; margin: 35px;">
        <a href="/home/my<?= !empty($unityVersion) ? '?unity_version=' . $unityVersion : "" ?>">我的</a>
      </div>

      <img decoding="async"  src="<?= $image ?>" alt="First slide" class="img-w-100">
      <div class="text-content">
        <div class="text-bg">
        </div>
        <div class='p-l-40'>
          <div class="fs-30 bold w-100 text-FF">
            审核剧本
          </div>
          <div class="fs-24  w-100 text-FF m-t-30">
            这是用于审核的剧本
          </div>
          <div class="btn-m-green m-t-30 float-right m-r-20 <?= !empty($unityVersion) ? 'play_btn' : '' ?>">
            <?php
            if (empty($unityVersion)) {
                  echo '<a href="detail?story_id=12">了解详情</a>';
                } else {
                  echo '进入游戏';
                }
            ?>
            <!--<img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
          </div>
        </div>
      </div>
    </div>
    <?php
    }else if (!empty($user) &&
      $user->user_type == \common\models\User::USER_TYPE_INNER
    && $user->mobile != 101
  && $user->mobile < 90)
  {
    ?>
    <div class="item">
      <!-- onclick="Unity.call('WebViewOff&StartARScene');" -->

      <div class="btn-m-green m-t-30 float-right m-r-20" style="position: absolute; right: 0px; margin: 35px;">
        <a href="/home/my?unity_version=<?= $unityVersion ?>">我的</a>
      </div>

      <img decoding="async"  src="<?= $image ?>" alt="First slide" class="img-w-100">
      <div class="text-content">
        <div class="text-bg">
        </div>
        <div class='p-l-40'>
          <div class="fs-30 bold w-100 text-FF">
            测试剧本
          </div>
          <div class="fs-24  w-100 text-FF m-t-30">
            这是用于测试的剧本
          </div>
          IS DEBUG:<input type="text" name="isDebug" value="1" style="color: yellow; padding: 10px; font-size: 50px;">
          SID: <input type="text" name="storyId" value="4" style="color: white; padding: 10px; font-size: 50px;">
          <div class="btn-m-green m-t-30 float-right m-r-20 play_btn">
            <?php

              if (empty($unityVersion)) {
                echo '已经购买，请联系客服！';
              } else {
                echo '进入游戏';
              }
            ?>
            <!--<img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
          </div>
        </div>
      </div>
    </div>
    <?php

  }
  ?>
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
    <div class="btn-m-green m-t-30 float-right m-r-20" style="position: absolute; right: 0px; margin: 35px;">
      <a href="/home/my<?= !empty($unityVersion) ? '?unity_version=' . $unityVersion : '' ?>">我的</a>
    </div>
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
        <?php
        if (empty($unityVersion) && 1 != 1) {
          ?>
            <div class="btn-m-green m-t-30 float-right m-r-20 show_detail" d-target="#story_detail_<?= $story->id ?>">
              <a href="detail?story_id=<?= $story->id ?>">了解详情</a>
            </div>
          <?php
        }
        ?>
        <div class="btn-m-green m-t-30 float-right m-r-20 <?= ($story->story_status == \common\models\Story::STORY_STATUS_ONLINE && !empty($unityVersion)) ? 'play_btn' : ''; ?>">
          <?php
          if ($story->story_status == \common\models\Story::STORY_STATUS_OPEN_WAIT) {
            echo '等待开放';
          } else {
            if (!empty($unityVersion) && !empty($story->latest_unity_version)
            && (\common\helpers\Common::compareUnityVersion($unityVersion, $story->latest_unity_version) < 0)
            ) {
                // 不满足最低版本
                echo '您需要升级以后访问！';
            } else {
              if ($orderStatus == \common\models\Order::ORDER_STATUS_PAIED
                  || $orderStatus == \common\models\Order::ORDER_STATUS_COMPLETED
              ) {
                if (empty($unityVersion)) {
                  if (file_exists(Yii::getAlias('@frontend/web/' . $story->id . '.txt'))) {
                    echo '<a href="detail?story_id=' . $story->id . '">了解详情</a>';
                  } else {
                    echo '敬请期待';
                  }
                } else {
                  echo '进入游戏';
                }
              } else {
                if ($story->extend->curr_price == 0 && !empty($unityVersion)) {
                  echo '限免';
                } else {
                  if (!empty($unityVersion)) {
                    if ($story->extend->curr_price != $story->extend->price) {
                      echo '￥' . $story->extend->curr_price . ' <span class="text-line-through">（￥' . $story->extend->price . '）</span>';
                    } else {
                      echo '￥' . $story->extend->price;
                    }
                  } else {
                    if (file_exists(Yii::getAlias('@frontend/web/' . $story->id . '.txt'))) {
                      echo '<a href="detail?story_id=' . $story->id . '">了解详情</a>';
                    } else {
                      echo '敬请期待';
                    }
                  }
                }
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
<footer class="footer">
  <div class="container">
    <!--Footer Info -->
    <div class="row footer-info text-center">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <span class="margin-10 footer-m-span white" style="color: white;"><?= !empty($unityVersion) ? '版本号：' . $unityVersion : ''?> &nbsp; 您有任何问题，欢迎联系我们：18500041193，也可以发邮件：<a href="mailto:choicexie@163.com">choicexie@163.com</a></a></span><br>
        <span class="margin-10 footer-m-span white" style="color: white;">Copyright © 2023-<?= Date('Y') ?> 庄生科技 zspiritx.com.cn 版权所有</span><br>
        <span class="margin-10 footer-m-span"><a href="https://beian.miit.gov.cn" class="white">京ICP备2023021255号</a></span>
      </div>
    </div>
    <!-- End Footer Info -->
  </div>
</footer>
</div>

