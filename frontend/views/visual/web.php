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

/**
 * @var \common\models\QA $qa
 */

\frontend\assets\Marginh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '知识库';

?>
<style>
    body {
        width: 2000px;
        margin-top: 0px;
        padding-top: 0px;
    }
    impo {
        font-weight: bold;
        font-size: 24px;
        color: red;
    }
</style>

<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<div class="w-100 m-auto">
<!--    <div class="btn-m-green m-t-30  m-l-30" style="top: 0px;">-->
<!--        <a href="/myh5/my?user_id=--><?php //=$userId?><!--&session_id=--><?php //=$sessionId?><!--&story_id=--><?php //=$storyId?><!--">上一页</a>-->
<!--    </div>-->
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 0px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border2">
                    <div class="btn-m-green m-t-30  m-l-30" style="position: absolute; right: 5px; top: -60px;" id="return_btn">
                        返回
                    </div>
                    <div class="npc-name" style="background-color: #000; color: #DAFC70">
                        亨利·福特
                    </div>

            <div class="row" id="answer-box">
                <div class="m-t-30 col-sm-6 col-md-12" style="float: left; width: 200px;">
                    亨利·福特<br>
                    美国福特汽车创始人<br>
                    世界上第一个用“流水线”生产汽车 的人<br>
                    1966年亨利·福特二世在“勒芒”比赛 中，凭借GT40的硬件能力，以及由谢尔比和肯·迈尔斯组成的传奇车队，最终战胜法拉利，赢下比赛<br>
                </div>

                <div class="m-t-30 col-sm-6 col-md-12">
                    <img width="400" src="https://bkimg.cdn.bcebos.com/pic/d4628535e5dde71190ef0bff54bcd91b9d16fdfacb24?x-bce-process=image/format,f_auto/quality,Q_70/resize,m_lfit,limit_1,w_536" />
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
<div class="modal fade" id="knowledge_detail" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content fs-30 bold w-100 text-FF title-box-border">
                <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 5px;right: 15px;">
                    <div><img src="../../static/img/qa/close_btn.png" alt="" class="img-36  d-inline-block m-r-10 vertical-mid"></div>
                </span>
            <!--                <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">-->
            <div>
                <div class="npc-name" id="knowledge_title">

                </div>

                <div class="row" id="knowledge_image">

                </div>
                <div id="knowledge_desc">

                </div>
                <div>

                    <!--                            <div class="btn-m-green m-t-30 float-right m-r-20" id="dialog_return_btn" target_id="baggage_detail">-->
                    <!--                                返回-->
                    <!--                            </div>-->

                </div>
            </div>
        </div>
    </div>

</div>




