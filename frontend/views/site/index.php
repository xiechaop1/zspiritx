<?php

/* @var $this yii\web\View */

\frontend\assets\Qah5Asset::register($this);

$this->title = '庄生科技';
?>
<!--<style>-->
<!--    div {-->
<!--        text-align: center;-->
<!--        color: white;-->
<!--    }-->
<!--</style>-->


<div class="p-20 bg-black" style="text-align: center; ">
    <span style="font-size: 36px; font-weight: bold; color: white;">庄生科技</span>
</div>

<div class="p-20 bg-black" style="text-align: center; color: white; font-size: 24px;">
    庄生科技，全面打造AR剧本杀，主要面向儿童群体，打造儿童AR剧本杀<br>
    目前<span style="color: red;">陶然亭</span>、<span style="color: red;">凯德茂大峡谷</span>等项目正在建设中，敬请期待！
</div>
<hr>
<div class="p-20 bg-black" align="center">
    <div class="w-60 p-30  m-b-10">
        <div class="w-1-0 d-flex">
            <div class="fs-30 bold w-100 text-FF title-box-border">
                <div style="padding: 15px; float: left;">
                    <img src="<?= $qrCode['huawei']; ?>" alt="" style="width: 200px; height: 200px; border: 2px white solid;" class="img-responsive d-block"/>
                    <br>
                    华为下载
                </div>
<!--                <div style="padding: 15px; float: left;">-->
<!--                    <img src="--><?php //= $qrCode['ios']; ?><!--" alt="" style="width: 200px; height: 200px;" class="img-responsive d-block"/>-->
<!--                    <br>-->
<!--                    iOS下载-->
<!--                </div>-->

                <!--<div class="hpa-ctr">
                    <img src="../../img/qa/btn_播放_nor@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>
                    播放语音
                </div>-->
            </div>
        </div>
    </div>
</div>
<hr>

<div style="text-align: center; color: white;">
    Copyright © 2004-2023  庄生科技 zspiritx.com.cn 版权所有
</div>
<div style="text-align: center; color: white;">
    <a href="https://beian.miit.gov.cn">京ICP备2023021255号</a>
</div>