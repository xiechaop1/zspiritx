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
        <div class="col-md-6 col-sm-6">
            <img class="img-responsive " src="../../static/image/logo.png" />
        </div>
         <div class="col-md-6 col-sm-6">
            <h1 class="logo-t">灵镜</h1>
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
                <div class="btn-m-green m-t-30 float-right m-r-20" id="login_btn">
                  提交
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

