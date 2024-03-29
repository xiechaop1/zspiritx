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
    <div class="btn-m-green m-t-30  m-l-30" style="top: 0px;">
        <a href="/myh5/my?user_id=<?=$userId?>&session_id=<?=$sessionId?>&story_id=<?=$storyId?>">上一页</a>
    </div>
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border2">
                    <div class="btn-m-green m-t-30  m-l-30" style="position: absolute; right: 5px; top: -60px;" id="return_btn">
                        返回
                    </div>
                    <div class="npc-name" style="background-color: #000; color: #DAFC70">
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

                    $isUnread = '';
                    if ($userKnowledge[$item->id]['is_read'] == 0) {
                        $isUnread = '<img id="unread_' . $userKnowledge[$item->id]['id'] . '" src="../../static/img/qa/unread.png" style="margin-left: 15px;"></img>';
                    }
                echo '
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="answer-border">
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
                        <label id="knowledge_' . $item->id . '" class="form-check-label fs-30 answer-btn knowledge-title" for="legal_person_yes_' . $item->id . '">
                            <span class="answer-tag">' . $showSort . '</span>
                    <span style="padding-left: 90px; ">'. $showTxt . $isUnread .  '</span>
                    <input type="hidden" name="knowledge_id" value="' . $item->id . '">
                    <input type="hidden" name="user_knowledge_id" value="' . $userKnowledge[$item->id]['id'] . '">
                    <input type="hidden" name="knowledge_title" value="' . mb_substr($label, 0 , 8) . (mb_strlen($label) > 8 ? '...' : '') . '">
                    <input type="hidden" name="knowledge_image" value="' . (!empty($image) ? \common\helpers\Attachment::completeUrl($image) : '') . '">
                    <input type="hidden" name="knowledge_content" value="' . htmlentities($content) . '">
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

<?php
if (!empty($showKnowledgeId)) {
?>
    <script>
        window.onload = function () {
            var obj = $('#knowledge_<?= $showKnowledgeId ?>');
            var user_knowledge_id = obj.find("input[NAME='user_knowledge_id']").val();
            var knowledge_title = obj.find("input[NAME='knowledge_title']").val();
            var knowledge_image = obj.find("input[NAME='knowledge_image']").val();
            var knowledge_desc_code = obj.find("input[NAME='knowledge_content']").val();

            $('#knowledge_title').html(knowledge_title);
            if (knowledge_image != '') {
                $('#knowledge_image').html('<img src=' + knowledge_image + ' style="width: 100%; height: auto;">');
            } else {
                $('#knowledge_image').html('');
            }
            var knowledge_desc = unescape(knowledge_desc_code);
            $('#knowledge_desc').html(knowledge_desc);

            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/knowledge/set_read',
                data:{
                    user_knowledge_id:user_knowledge_id
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    $.alert("网络异常，请检查网络情况");
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    //新消息获取成功
                    if(obj["code"]==200){
                        $('#unread_' + user_knowledge_id).hide();
                    }
                    //新消息获取失败
                    else{
                        alert(obj.msg)
                    }

                }
            });

            $('#knowledge_detail').modal('show');
        }
    </script>

    <?php
}?>



