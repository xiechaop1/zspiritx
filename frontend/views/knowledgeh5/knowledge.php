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
<style>
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
    <div class="btn-m-green m-t-30  m-l-30">
        <a href="/myh5/my?user_id=<?=$userId?>&session_id=<?=$sessionId?>&story_id=<?=$storyId?>">上一页</a>
    </div>
    <div class="btn-m-green m-t-30  m-l-30" id="return_btn">
        返回
    </div>
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border">
                    <div class="npc-name">
                        <?= $knowledgeClass == \common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL ? '知识' : '任务'; ?>
                    </div>

            <div class="row" id="answer-box">
                <?php
                foreach ($model as $item) {
                    $label = !empty($item->title) ? $item->title : $item->content;
                    $content = !empty($item->content) ? $item->content : '-';
                    $image = !empty($item->image) ? $item->image : '';

                    $status = \common\models\UserKnowledge::KNOWLDEGE_STATUS_INIT;
                    if (!empty($userKnowledgeMap[$item->id])) {
                        $status = $userKnowledgeMap[$item->id];
                    } else {
                        // 临时这么处理，这样就只会显示正在进行和已经完成的任务/知识了
                        continue;
                    }

                    if ($knowledgeClass == \common\models\Knowledge::KNOWLEDGE_CLASS_NORMAL) {
                            $spanTxtTpl = '<span style="color: white">%t</span>';
                            $spanSortTpl = '<span style="color: white">%t</span>';
                    } else {
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
                    }
                    $txt = $label;
//                    $sortBy = $item->sort_by;

                    $sortBy = '💡';

                    $showTxt = str_replace('%t', $txt, $spanTxtTpl);
                    $showSort = str_replace('%t', $sortBy, $spanSortTpl);
                echo '
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                        <label id="knowledge_' . $item->id . '" class="form-check-label fs-30 answer-btn knowledge-title" for="legal_person_yes_' . $item->id . '">
                            <span class="answer-tag">' . $showSort . '</span>
                    <span style="padding-left: 90px; ">'. $showTxt .  '</span>
                    <input type="hidden" name="knowledge_id" value="' . $item->id . '">
                    <input type="hidden" name="knowledge_title" value="' . mb_substr($label, 0 , 8) . (mb_strlen($label) > 8 ? '...' : '') . '">
                    <input type="hidden" name="knowledge_image" value="' . !empty($image) ? \common\helpers\Attachment::completeUrl($image) : '' . '">
                    <input type="hidden" name="knowledge_content" value="' . $content . '">
                    </label>
                    </div>
                    </div>
                   
                    ';
//                . '<div class="knowledge-content" style="text-align: left; font-size: 20px; display: none;"><hr style="color: #ffffff; border: 1px;">' . $content;
//                if (!empty($image)) {
//                    echo '<hr style="color: #ffffff; border: 1px;">' . '<img src="' . \common\helpers\Attachment::completeUrl($image) . '" style="width: 100%; height: auto;">';
//                }
//                    echo '</div>' . '
//                     </div>
//                </div>
//                ';
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
                <div class="row" id="knowledge_desc">

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

<?php
if (!empty($showKnowledgeId)) {
?>
    <script>
        window.onload = function () {
            var obj = $('#knowledge_<?= $showKnowledgeId ?>');
            var knowledge_title = obj.find("input[NAME='knowledge_title']").val();
            var knowledge_image = obj.find("input[NAME='knowledge_image']").val();
            var knowledge_desc = obj.find("input[NAME='knowledge_content']").val();

            $('#knowledge_title').html(knowledge_title);
            $('#knowledge_image').html(knowledge_image);
            $('#knowledge_desc').html(knowledge_desc);

            $('#knowledge_detail').modal('show');
        }
    </script>

    <?php
}?>



