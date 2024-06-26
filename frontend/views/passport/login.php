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

$this->title = 'AR儿童剧本杀';

?>
<!---->
<div id="loginform" class="w-100 m-auto">

  <div class="p-20 bg-black">
    <div class="w-100 p-30  m-b-10" style="position: absolute; top: 100px;">
      <div class="row m-b-40">
        <div class="col-md-1 col-sm-3" style="margin-left: 20px;">
            <img class="img-responsive " src="../../static/image/lingjing_icon.png" style="width: 120px; height: 120px;" />
        </div>
         <div class="col-md-6 col-sm-8">
            <h1 class="logo-t">灵镜新世界</h1>
         </div>
      </div>



      <div class="w-1-0 d-flex">
        <div class="fs-30 bold w-100 text-FF title-box-border">
          <div class="npc-name">
            注册 / 登录
          </div>

          <div class="row" id="login-box">
            <div class="m-t-30 col-sm-12 col-md-12">
              <div class="login-box-sm">
                手机号：<br><input class="answer-border w-100 m-b-10" type="text" name="mobile" value="" id="mobile" style="margin: 5px;" ><br>
                验证码：<br>
                <div class="m-b-10">
                   <input class="answer-border w-50 m-b-10" type="text" name="verifycode" value="" id="verifycode" size="10" style="margin: 5px;" >
                   <a href="javascript:void(0);" id="get_verifycode">获取验证码</a>
                </div>
                 <div class="form-check form-check-inline m-t-5">
                     <input class="form-check-input" type="checkbox" name="agreement" value="2" id="agreement1">
                     <label class="form-check-label fs-30 text-66" for="agreement1">
                         <span></span>
                     </label>
                     <a href="https://zspiritx.oss-cn-beijing.aliyuncs.com/doc/zspiritx_useragreement.docx">用户协议</a><br>
                 </div>

                <input type="hidden" name="is_debug" id="login_is_debug" value="">
                <input type="hidden" name="story_id" id="login_story_id" value="">
                  <input type="hidden" name="unity_version" id="unity_version" value="<?= $unityVersion ?>">
                <div class="btn-m-green m-t-30 float-right m-r-20" id="login_btn">
                  提交
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

<div class="row" style="margin-top: 20px;">
        <div class="col-md-1 col-sm-2">
            <img class="img-responsive " src="../../static/image/logo.png" />
        </div>
        <!-- FOOTER -->
        <footer class="footer">
            <div class="container">
                <!--Footer Info -->
                <div class="row footer-info text-center">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <span class="margin-10 footer-m-span white">Copyright © 2023-2023 庄生科技 zspiritx.com.cn 版权所有 版本号：<?= $unityVersion ?></span><br>
                        <span class="margin-10 footer-m-span"><a href="https://beian.miit.gov.cn" class="white">京ICP备2023021255号</a></span>
                    </div>
                </div>
                <!-- End Footer Info -->
            </div>
        </footer>
        <!-- END FOOTER -->
</div>

    </div>

  </div>
</div>

</div>

