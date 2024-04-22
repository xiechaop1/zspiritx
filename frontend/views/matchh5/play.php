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

$this->title = '比赛结果';

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
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border2">
                    <div class="btn-m-green m-t-30  m-l-30" style="position: absolute; right: 5px; top: -60px;" id="return_btn">
                        返回
                    </div>
                    <div class="npc-name" style="background-color: #000; color: #DAFC70">
                        比赛结果
                    </div>
<div id="top_flow" style="position: sticky; top: 0px; background-color: rgba(0,0,0,0.5); z-index: 99999999; padding: 25px; border-radius: 15px;">比赛开始</div>
            <div class="row" id="answer-box">
                <?php
                $matchFlow = !empty($matchDetail['flow']) ? $matchDetail['flow'] : [];
                foreach ($matchFlow as $item) {
                            $spanTxtTpl = '<span style="color: white">%t</span>';
                            $spanSortTpl = '<span style="color: white">%t</span>';

                    $txt = !empty($item['txt']) ? $item['txt'] : '';
                    $sortBy = !empty($item['ct']) ? $item['ct'] : '0';
//                    $sortBy = $item->sort_by;

//                    $sortBy = '🏆';
                    if (!empty($item['icon'])) {
                        $roundCt = $item['icon'];
                    } else {
                        if ($sortBy < 999) {
                            $roundCt = '🏁';
                        } else {
                            $roundCt = '🏆';
                        }
                    }

                    $showTxt = str_replace('%t', $txt, $spanTxtTpl);
                    $showSort = str_replace('%t', $roundCt, $spanSortTpl);

                    $isUnread = '';
                echo '
                <div class="m-t-30 col-sm-12 col-md-12">
                    <div class="match_msg" id="msg_' . $sortBy . '" style="display:none;">
                        <div class="answer-border">
                        <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $sortBy . '" id="legal_person_yes_' . $sortBy . '" > -->
                            <span class="answer-tag">' . $showSort . '</span>
                    <span style="padding-left: 90px; ">'. $showTxt .  '</span>
                    </div>
                    </div>
                    </div>
                   <input type="hidden" id="match_detail_round_' . $sortBy . '" value="' . $roundCt . '">
                   <input type="hidden" id="match_detail_title_' . $sortBy . '" value="' . $sortBy . '">
                   <input type="hidden" id="match_detail_desc_' . $sortBy . '" value="' . strip_tags($txt) . '">
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
                    <div class="btn-m-green m-t-30 float-right m-r-20" style="position: absolute; bottom: 0px;" id="return_btn1">
                        返回
                    </div>
                </div>
            </div>

        </div>
        </div>

    </div>
<div class="modal fade" id="match_detail" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content fs-30 bold w-100 text-FF title-box-border">
                <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 5px;right: 15px;">
                    <div><img src="../../static/img/qa/close_btn.png" alt="" class="img-36  d-inline-block m-r-10 vertical-mid"></div>
                </span>
            <!--                <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">-->
            <div>
                <div class="npc-name" id="match_detail_title">

                </div>

                <div class="row" id="match_detail_round" style="width: 100%; text-align: center; font-size: 60px;">

                </div>
                <div id="match_detail_desc" style="width: 100%; text-align: center; font-size: 60px;">

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
<script>
    window.onload = function () {
        var i = 0;
        var max = <?= $ct ?>;

        var dataContent = <?= $matchAllFlowJson ?>;
        var dataCon=$.toJSON(dataContent);
        var obj = eval( "(" + dataCon + ")" );

        var matchTimer = setInterval(function() {
            if (i > max) {
                showMsg(999);
                clearInterval(matchTimer);
            }
            console.log(i);
            // $('#msg_' + i).show();
            // if ($('#msg_' + i).length > 0) {
            //     $('#msg_' + i).get(0).scrollIntoView();
            // }
            showMsg(i);
            showFlow(i, obj);
            i++;
        }, 400);

    };

    function showMsg(i) {
        $('#msg_' + i).show();
        if ($('#msg_' + i).length > 0) {
            $('#msg_' + i).get(0).scrollIntoView();
            console.log($('#match_detail_round_' + i).val());
            var title = '第' + $('#match_detail_title_' + i).val() + '圈';
            if (i == '999') {
                title = '结果';
            }
            $('#match_detail_title').html(title);
            // $('#match_detail_round').html($('#match_detail_round_' + i).val());
            $('#match_detail_desc').html($('#match_detail_round_' + i).val() + ' ' + $('#match_detail_desc_' + i).val());
            $('#match_detail').modal('show');
        }
    }

    function showFlow(j, obj) {
        var item = obj[j];
        if (j >= obj.length) {
            $('#top_flow').html('');
            return true;
        }
        var txt = item.txt;
        $('#top_flow').html(txt);
    }


</script>


