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

$this->title = '我的背包';

?>

<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="target_story_model_id" value="<?= $targetStoryModelId ?>">
<input type="hidden" name="target_story_model_detail_id" value="<?= $targetStoryModelDetailId ?>">
<input type="hidden" name="target_model_id" value="<?= $targetModelId ?>">
<div class="w-100 m-auto">
    <div class="btn-m-green m-t-30  m-l-30" id="return_btn">
                        返回
    </div>
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        背包
                    </div>

            <div class="row" id="answer-box">
                <?php
                foreach ($model as $item) {
                    $label = !empty($item->storyModel->story_model_name) ? $item->storyModel->story_model_name : $item->model->model_name;
                    $desc = !empty($item->storyModel->story_model_desc) ? $item->storyModel->story_model_desc : $item->model->model_desc;
                    $txt = $label;
                    if (!empty($desc)) {
                        $txt .= '： ' . $desc;
                    }


                    if (!empty($item->storyModel)
                        && $item->storyModel->use_allow == \common\models\StoryModels::USE_ALLOW_NOT
                    ) {
                        $baggageName = 'baggage_nouse';
                    } else {
                        $baggageName = 'baggage';
                    }
                echo '
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="' . $baggageName . '" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_' . $item->id . '" style="padding-left: 90px;">
                            <span class="answer-tag">' . $item->use_ct . '</span>
                    '. $txt . '
                    </label>
                    </div>
                </div>
                ';
                }
                ?>
                <!--checkbox -->
                <!--
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="checkbox" name="answer" value="1" id="checkbox_1" >
                        <label class="form-check-label fs-30 answer-btn" for="checkbox_1">
                            8跟
                        </label>
                    </div>
                </div>-->


<!--
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes_A" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_A">
                            <span class="answer-tag">A</span>
                            8跟
                        </label>
                    </div>
                </div>
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes_B" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_B">
                            <span class="answer-tag">B</span>
                            6跟
                        </label>
                    </div>
                </div>
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes_C" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_C">
                            <span class="answer-tag">C</span>
                            5跟
                        </label>
                    </div>
                </div>
                <div class="m-t-30 col-sm-12 col-md-6">
                    <div class="answer-border">
                        <input class="form-check-input" type="radio" name="answer" value="1" id="legal_person_yes_D" >
                        <label class="form-check-label fs-30 answer-btn" for="legal_person_yes_D">
                            <span class="answer-tag">D</span>
                            3跟
                        </label>
                    </div>
                </div>
-->

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



