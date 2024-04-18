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

$this->title = '我的背包';

?>
<style>
    .bag_selected {
        border: 1px solid rgba(218, 252, 112, 0.8);
        background-color: rgba(155, 252, 112, 0.2);
    }

    .btn-disable {
        color: #999;
    }

</style>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="target_story_model_id" value="<?= $targetStoryModelId ?>">
<input type="hidden" name="target_story_model_detail_id" value="<?= $targetStoryModelDetailId ?>">
<input type="hidden" name="target_model_id" value="<?= $targetModelId ?>">

<input type="hidden" name="selected_story_model_ids" value="">
<input type="hidden" name="use_btn_disable" value="0">
<input type="hidden" name="combine_btn_disable" value="0">
<div class="w-100 m-auto">
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border2">
                    <div class="btn-m-green m-t-30  m-l-30" style="position: absolute; right: 5px; top: -60px;" id="return_btn">
                        返回
                    </div>
                    <div class="npc-name" style="background-color: #000; color: #DAFC70">
                        背包
                    </div>
            <div class="row" id="answer-box">
                <?php
                foreach ($model as $item) {
                    $label = !empty($item->storyModel->story_model_name) ? $item->storyModel->story_model_name : $item->model->model_name;
                    $desc = !empty($item->storyModel->story_model_desc) ? $item->storyModel->story_model_desc : $item->model->model_desc;
                    $txt = $label;
//                    if (!empty($desc)) {
//                        $txt .= '： ' . $desc;
//                    }


                    if (!empty($item->storyModel)
                        && $item->storyModel->use_allow == \common\models\StoryModels::USE_ALLOW_NOT
                    ) {
                        $baggageName = 'baggage_nouse';
                    } else {
                        $baggageName = 'baggage';
                    }
                ?>
                <div class="m-t-30 col-sm-3 col-md-12" style="padding: 5px; " id="baggage_area">
                    <div class="answer-border2" style="height: 260px;">
                        <input class="form-check-input" type="radio" name="<?= $baggageName ?>" value="<?= $item->id ?>" id="legal_person_yes_<?= $item->id ?>" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_<?= $item->id ?>" style="padding-top: 20px;">
                            <span class="answer-tag2"><?= $item->use_ct ?></span>
                            <div style="clear:both; height: 120px;">
                            <?php
                            if (!empty($item->storyModel->icon)) {
                                echo '<img src="' . \common\helpers\Attachment::completeUrl($item->storyModel->icon, true) . '" class="img-120 m-r-10" style="border-radius: 20px;" title="' . $txt . '">';
                            } else {
                                echo $txt;
                            }
                            ?>
                            </div>
                            <div class="answer-txt">
                                <?= $txt ?>
                            </div>

                    </label>
                    </div>
                </div>
                <?php
                }
                ?>

            </div>
<!--                    <div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
                        返回
                    </div> -->
                </div>
            </div>

        </div>
        </div>

    <div class="modal fade" id="baggage_detail" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content fs-30 bold w-100 text-FF title-box-border">
                <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 5px;right: 15px;">
                    <div><img src="../../static/img/qa/close_btn.png" alt="" class="img-36  d-inline-block m-r-10 vertical-mid"></div>
                </span>
<!--                <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">-->
                    <div>
                            <div class="npc-name" id="baggage_title">

                            </div>

                            <div class="row" id="baggage_html">

                            </div>
                            <div class="row" id="baggage_desc">

                            </div>
                        <div>

                        <div class="btn-m-green m-t-30 float-right m-r-20" id="dialog_return_btn" target_id="baggage_detail" need_refresh="0">
                            返回
                        </div>

                    </div>
            </div>
        </div>
    </div>

</div>
<div style="position: fixed; bottom: 0px; margin:10px; width: 100%;">
    <div class="w-100 p-30  m-b-10">
        <div class="w-1-0 d-flex">
            <div class="fs-30 bold w-100 text-FF">
    <div class="btn-m-green m-t-30  m-l-30 use_btn" style="position: absolute; left: 5px; top: -50px;" id="use_btn" act="1">
        使用
    </div>
                <div class="btn-m-green m-t-30  m-l-30 use_btn" act="2" style="position: absolute; left: 155px; top: -50px;" id="combine_btn">
                    组合
                </div>
            </div>
        </div>
    </div>
</div>

</div>



