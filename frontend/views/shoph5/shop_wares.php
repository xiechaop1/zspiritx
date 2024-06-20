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

$this->title = '商店';

?>
<style>
    <?php
if ( !empty($params['story_model_class']) && $params['story_model_class'] == \common\models\StoryModels::STORY_MODEL_CLASS_PET ) {
    echo '.bag_selected {
        border: 1px solid rgba(218, 162, 252, 0.8);
        background-color: rgba(155, 112, 182, 0.2);
    }
    ';
} else {
    echo '
    .bag_selected {
        border: 1px solid rgba(218, 252, 112, 0.8);
        background-color: rgba(155, 252, 112, 0.2);
    }
    ';
}
 ?>
    a {
        color: #DAFC70;
    }

    .btn-disable {
        color: #999;
    }
    /*.npc-name {*/
    /*    border: 2px solid rgba(218, 162, 252, 0.8);*/
    /*}*/

    .buy_btn {
        border-radius: 24px 24px 24px 24px;
        border: 2px solid #DAFC70;
        background-color: #DAFC70;
        font-size: 24px;
        color: #1F2628;
        line-height: 12px;
        display: inline-block;
        padding: 5px 20px 5px 20px;
        vertical-align: top;
        text-align: center;
        width: 200px;
    }

</style>
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">

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
                        <?= $title ?>
                    </div>

            <div class="row" id="answer-box" style="margin-top: 20px;">
                <?php
                foreach ($model as $item) {
                    $label = !empty($item->ware_name) ? $item->ware_name : $item->storyModel->story_model_name;
                    $desc = !empty($item->intro) ? $item->intro : ' - ';
                    if (empty($desc)) {
                        !empty($item->storyModel->story_model_desc) ? $item->storyModel->story_model_desc : '-';
                    }
                    $icon = !empty($item->icon) ? $item->icon : '';
                    if (empty($icon)) {
                        $icon = !empty($item->storyModel->icon) ? $item->storyModel->icon : '';
                    }
                    $txt = $label;

                ?>
                <div class="m-t-100 col-sm-12 col-md-12" style="padding: 5px; " id="baggage_area">
                    <div class="answer-border2">
<!--                        <input class="form-check-input" type="radio" name="item" value="--><?php //= $item->id ?><!--" id="legal_person_yes_--><?php //= $item->id ?><!--" >-->
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_<?= $item->id ?>" style="padding-top: 20px;">
<!--                            <span class="answer-tag2">--><?php //= $item->store_ct ?><!--</span>-->
                            <div class="col-sm-2" style="float: left;">
                            <?php
                            if (!empty($icon)) {
                                echo '<img src="' . \common\helpers\Attachment::completeUrl($icon, true) . '" width="80" class=" m-r-10" style="border-radius: 20px;" title="' . $txt . '">';
                            } else {
                                echo $txt;
                            }
                            ?>
                            </div>
                            <div class="col-sm-6" style="float: left; width: 350px; text-align: left;">
                                <?= $txt ?>
                                <br>
                                <span class="fs-20" style="color: #999">
                                <?= $desc ?>
                                </span>
                            </div>
                            <div class="col-sm-4" style="float: left;">

                                <div class="buy_btn" id="buy_btn" act="1">
                                    <?php
                                    echo '<div style="float: left;">';
                                    switch ($item->pay_way) {
                                        case \common\models\ShopWares::PAY_WAY_MONEY:
                                            echo '<img src="../../static/img/qa/money.png" width="50">';
                                            break;
                                        case \common\models\ShopWares::PAY_WAY_SCORE:
                                        default:
                                            echo '<img src="../../static/img/qa/gold.png" width="50">';
                                            break;
                                    }
                                    echo '</div>';

                                    echo '<div style="float: left; line-height: 150%; text-align: right; ">';
                                    if (!empty($item->discount)) {
                                    echo '<span class="fs-32 bold">' . \common\helpers\Common::formatNumberToStr($item->discount) . '</span><br>';
                                    echo '<span class="fs-24 bold" style="color: #666666; text-decoration: line-through; ">' . \common\helpers\Common::formatNumberToStr($item->price) . '</span>';
                                    echo '</div>';
                                }
                                else {
                                    echo '<div style="float: left; line-height: 150%;">';
                                    echo '<span class="fs-32 bold">' . \common\helpers\Common::formatNumberToStr($item->price) . '</span>';
                                    echo '</div>';
                                }

                                ?>
                                </div>

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
            <div class="fs-30 bold w-100 text-FF" style="float: left;">

            </div>
            <div class="fs-30 bold w-100 text-FF" style="float: left; position: relative; right: 0px;">
                <div style="position: absolute; top: -50px;right: 50px; ">
                    <img src="../../static/img/qa/gold.png" width="50">
                    <?= \common\helpers\Common::formatNumberToStr($userScore->score, true) ?>
                </div>

            </div>

        </div>
    </div>
</div>

</div>



