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

$this->title = '灵镜新世界-我的';

?>
<style>
  .text-line-through {
    text-decoration: line-through;
    color: yellow;
  }
  a {
    color: yellow;
  }
</style>

<div style="position: absolute; top: 0px; z-index: 999; margin: 20px; color: white; font-size: 24px;">
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
<div class="w-100 m-auto">
    <input type="hidden" id="unity_version" name="unity_version" value="<?= $unityVersion ?>">
  <div class="p-20 bg-black w-100 m-t-80" style="position: relative; left: 0px; top: 30px;">
    <div class="w-100 p-30  m-b-10">
      <div class="w-1-0 d-flex">
        <div class="fs-30 bold w-100 text-FF title-box-border ">
          <div class="npc-name">
            我的
          </div>

            <div class="row" id="answer-box">
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                        <label class="form-check-label fs-30 answer-btn">
                            <?= !empty($userInfo->user_name) ? $userInfo->user_name : $userId ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row" id="answer-box">
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                        <label class="form-check-label fs-30 answer-btn">
                            <!--                  <span class="answer-tag"></span>-->
                            <span ><a href="/home/orders<?= !empty($unityVersion) ? "?unity_version=" . $unityVersion : "" ?>">订单</a></span>
                        </label>
                    </div>
                </div>
            </div>
          <div class="row" id="answer-box">
            <div class="m-t-30 col-sm-12 col-md-12">
              <div class="answer-border">
                <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                <label class="form-check-label fs-30 answer-btn">
                  <!--                  <span class="answer-tag"></span>-->
                  <span ><a href="https://zspiritx.oss-cn-beijing.aliyuncs.com/doc/zspiritx_useragreement.docx">用户协议</a></span>
                </label>
              </div>
            </div>
          </div>
            <div class="row" id="answer-box">
              <div class="m-t-30 col-sm-12 col-md-12">
                <div class="answer-border">
                  <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                  <label class="form-check-label fs-30 answer-btn">
                    <!--                  <span class="answer-tag"></span>-->
                    <span ><a href="https://zspiritx.oss-cn-beijing.aliyuncs.com/doc/lingjing-userprivacyagreement.docx.docx">用户隐私协议</a></span>
                  </label>
                </div>
              </div>
            </div>
            <div class="row" id="answer-box">
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                        <?php
                        if (!empty($from) && $from == 'homebtn') {
                            ?>
                            <label class="form-check-label fs-30 answer-btn">
                                <!--                  <span class="answer-tag"></span>-->
                                <span ><a href="javascript:void(0)" class="return_back_btn">返回</a></span>
                            </label>
                            <?php
                        } else {
                        ?>
                        <label class="form-check-label fs-30 answer-btn">
                            <!--                  <span class="answer-tag"></span>-->
                            <span ><a href="/home/index<?= !empty($unityVersion) ? "?unity_version=" . $unityVersion : "" ?>">返回</a></span>
                        </label>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
          <div class="row" id="answer-box">
            <div class="m-t-30 col-sm-12 col-md-12">
              <div class="answer-border">
                <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                <label class="form-check-label fs-30 answer-btn">
<!--                  <span class="answer-tag"></span>-->
                  <span ><a href="javascript:void(0);" id="logout_btn">退出</a></span>
                </label>
              </div>
            </div>
          </div>
          <div class="row" id="answer-box">
            <div class="m-t-30 col-sm-12 col-md-12">
              <div class="answer-border">
                <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                <label class="form-check-label fs-30 answer-btn">
<!--                  <span class="answer-tag">K</span>-->
                  <span><a href="javascript:void(0);" id="delete_btn" style="color: grey;">注销</a></span>
                </label>

              </div>
            </div>

          </div>
          <!--                    <div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
                                  返回
                              </div> -->
        </div>
      </div>

    </div>
  </div>

</div>

</div>

<footer class="footer" style="bottom: 0px;">
    <div class="container">
        <!--Footer Info -->
        <div class="row footer-info text-center">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <span class="margin-10 footer-m-span white" style="color: white;"><?= !empty($unityVersion) ? '版本号：' . $unityVersion : ''?> &nbsp; 联系我们：18500041193</span><br>
                <span class="margin-10 footer-m-span white" style="color: white;">Copyright © 2023-<?= Date('Y') ?> 庄生科技 zspiritx.com.cn 版权所有</span><br>
                <span class="margin-10 footer-m-span"><a href="https://beian.miit.gov.cn" class="white">京ICP备2023021255号</a></span>
            </div>
        </div>
        <!-- End Footer Info -->
    </div>
</footer>



