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

$this->title = '知识库';

?>

<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="session_id" value="<?= $sessionId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<div class="w-100 m-auto">
    <div class="btn-m-green m-t-30  m-l-30" id="return_btn">
                        返回
    </div>
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        知识库
                    </div>

            <div class="row" id="answer-box">
                <?php
                foreach ($model as $item) {
                    $label = !empty($item->title) ? $item->title : $item->content;
                    $content = !empty($item->content) ? $item->content : '-';

                    $status = \common\models\UserKnowledge::KNOWLDEGE_STATUS_INIT;
                    if (!empty($userKnowledgeMap[$item->id])) {
                        $status = $userKnowledgeMap[$item->id];
                    }

                    if ($status == \common\models\UserKnowledge::KNOWLDEGE_STATUS_INIT) {
                        $spanTxtTpl = '<span style="color: white">%t</span>';
                        $spanSortTpl = '<span style="color: white">%t</span>';
                    } elseif ($status == \common\models\UserKnowledge::KNOWLDEGE_STATUS_PROCESS) {
                        $spanTxtTpl = '<span style="color: red">%t</span>';
                        $spanSortTpl = '<span style="color: red">%t</span>';
                    } elseif ($status == \common\models\UserKnowledge::KNOWLDEGE_STATUS_COMPLETE) {
                        $spanTxtTpl = '<span style="color: grey">%t</span>';
                        $spanSortTpl = '<span style="color: grey">%t</span>';
                    }
                    $txt = $label;
                    $sortBy = $item->sort_by;

                    $showTxt = str_replace('%t', $txt, $spanTxtTpl);
                    $showSort = str_replace('%t', $sortBy, $spanSortTpl);
                echo '
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                        <label class="form-check-label fs-30 answer-btn knowledge-title" for="legal_person_yes_' . $item->id . '">
                            <span class="answer-tag">' . $showSort . '</span>
                    <span style="padding-left: 90px; ">'. $showTxt .  '</span>
                    </label>
                   
                    ' . '<div class="knowledge-content" style="font-size: 18px; display: none;"><hr style="color: #ffffff; border: 1px;">' . $content . '</div>' . '
                     </div>
                </div>
                ';
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

    </div>

</div>


