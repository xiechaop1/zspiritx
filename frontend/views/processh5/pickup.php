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

\frontend\assets\Qah5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '拾取';

?>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        消息
                    </div>
                </div>
            </div>
            <div class="row" id="answer-box">
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <?= $msg ?>
                        <div class="btn-m-green m-t-30 float-right m-r-20" id="msg_return_btn">
                            返回
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

    </div>

</div>



<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-null" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                       请选择答案
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-right" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        恭喜您答对了
                    </div>
                    <div class="text-center m-t-30">
                        <?php echo $qa['st_answer']; ?>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-worry" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        很遗憾，打错了
                    </div>
                    <div class="m-t-40 bg-F5 p-20 fs-26 text-orange border-radius-r-5 border-radius-l-5">
                        <?php echo $qa['st_answer']; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
