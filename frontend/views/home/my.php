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

<div class="w-100 m-auto">

  <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
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
                  <!--                  <span class="answer-tag"></span>-->
                  <span style="padding-left: 90px; "><a href="https://zspiritx.oss-cn-beijing.aliyuncs.com/doc/zspiritx_useragreement.docx">用户协议</a></span>
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
                    <span style="padding-left: 90px; "><a href="https://zspiritx.oss-cn-beijing.aliyuncs.com/doc/lingjing-userprivacyagreement.docx.docx">用户隐私协议</a></span>
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
                            <span style="padding-left: 90px; "><a href="/home/index" id="logout_btn">返回</a></span>
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
                  <span style="padding-left: 90px; "><a href="javascript:void(0);" id="logout_btn">退出</a></span>
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
                  <span style="padding-left: 90px; "><a href="javascript:void(0);" id="delete_btn" style="color: grey;">注销</a></span>
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

