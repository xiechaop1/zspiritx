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

$this->title = '对战结果';

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
<input type="hidden" id="rtn_answer_type" value="<?= $storyMatch->ret == \common\models\StoryMatch::STORY_MATCH_RESULT_WIN ? '2' : 1 ?>">
<div class="w-100 m-auto">
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border2">
                    <div class="btn-m-green m-t-30  m-l-30 battle_return_btn" style="position: absolute; right: 5px; top: -60px;">
                        返回
                    </div>
                    <div class="npc-name" style="background-color: #000; color: #DAFC70">
                        对战结果
                    </div>
<div id="top_flow" style="position: sticky; top: 50px; background-color: rgba(0,0,0,0.5); z-index: 99999999; padding: 25px; border-radius: 15px;">对战开始</div>
            <div class="row" id="answer-box">
                <?php
                $matchFlow = !empty($matchDetail['flow']) ? $matchDetail['flow'] : [];
                $ct = 0;
                foreach ($matchFlow as $item) {
                            $spanTxtTpl = '<span style="color: white">%t</span>';
                            $spanSortTpl = '<span style="color: white">%t</span>';

                    $txt = !empty($item['txt']) ? $item['txt'] : '';
//                    $sortBy = !empty($item['ct']) ? $item['ct'] : '0';
////                    $sortBy = $item->sort_by;
//
////                    $sortBy = '🏆';
//                    if (!empty($item['icon'])) {
//                        $roundCt = $item['icon'];
//                    } else {
//                        if ($sortBy < 999) {
//                            $roundCt = '🏁';
//                        } else {
//                            $roundCt = '🏆';
//                        }
//                    }

                    $type = !empty($item['type']) ? $item['type'] : '';
                    switch ($type) {
                        case 1:
                        case 3:
                            $roundCt = '🛡️';
                            break;
                        case 2:
                        default:
                            $roundCt = '⚔️';
                            break;
                    }
//                    $roundCt = '⚔️';
                    $sortBy = $ct;
                    $ct++;

                    $showTxt = str_replace('%t', $txt, $spanTxtTpl);
                    $showSort = str_replace('%t', $roundCt, $spanSortTpl);
//                    var_dump($item);
//                    var_dump($txt);
//var_dump($showTxt);exit;
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
                    <div class="btn-m-green m-t-30 float-right m-r-20 battle_return_btn" style="position: absolute; bottom: 0px;">
                        返回
                    </div>
                </div>
            </div>

        </div>
        </div>

    </div>
<div class="modal fade" id="match_detail" tabindex="-1" style="position: sticky; display: none;opacity: 100;" aria-hidden="true">
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
                    <div id="match_result" style="position: absolute; width: 30px;  z-index: 999999999; display: none; font-size: 70px; color: yellow;"> </div>
                    <div style="float: left;">
                        <div id="avatar_hp" style="width: 200px; height: 20px; border: 1px white solid;">
                            <div style="width: 100%; height: 18px; background-color: green">&nbsp; </div>
                        </div>
                    <img src="https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/04/fsa4e285wczrdwe5i8hndhfraz2kbrzf.png?x-oss-process=image/format,png"
                         width="200" style="position: relative; left: 0px; z-index: 50" class="avatar_img">

                    </div>
                    <div style="float: left;">
                        <div id="rival_avatar_hp" style="position: relative; left: 50px; width: 200px; height: 20px; border: 1px white solid;">
                            <div style="width: 100%; height: 18px; background-color: green">&nbsp; </div>
                        </div>
                    <img src="https://zspiritx.oss-cn-beijing.aliyuncs.com/story_model/icon/2024/04/nriyjtfxp8piaje5k5ne8bmm47f6z6e5.png?x-oss-process=image/format,png"
                         width="200" class="rival_avatar_img" style="position: relative; left: 50px; z-index: 100">
                        <br>
                    </div>

                </div>
                <div id="number-floater" style="position: absolute; font-size: 70px; top: 36px; left: 250px; text-align: center; z-index: 9999999"></div>
                <div>

                    <!--                            <div class="btn-m-green m-t-30 float-right m-r-20" id="dialog_return_btn" target_id="baggage_detail">-->
                    <!--                                返回-->
                    <!--                            </div>-->

                </div>
            </div>
        </div>
    </div>

</div>
<div class="row modal fade" id="answer-right-box" style="top: 100px;">
    <div class="m-t-30 col-sm-12 col-md-12 p-40">
        <!--                    <img src="../../static/img/qa/Frame@2x.png" alt="" class="img-responsive  d-block m-auto"/>-->
        <img src="../../static/img/match/bc_win.png" alt="" class="img-responsive  d-block m-auto"/>
        <div style="clear:both; text-align: center;">
                        <span>
                            <!-- ../../static/img/qa/gold.gif -->
                    <img src="../../static/img/qa/gold.png" alt="" style="width: 125px; height: 125px;" class=""/>
                            </span>

            <span class="answer-detail" id="gold_score" style="color: yellow">

                        </span>
        </div>
        <br>
        <!--                    <div class="answer-title m-t-40">-->
        <!--                        恭喜您，挑战成功！-->
        <!--                    </div>-->
        <div class="btn-m-green m-t-30  m-l-30 confirm_btn">
            继续
        </div>
        <!--                    <div class="answer-detail m-t-40" style="line-height: 40px;">-->
        <!--                        --><?php //echo ($qa['st_answer'] != 'True' && $qa['st_answer'] != $qa['st_selected']) ? $qa['st_answer'] : ''; ?>
        <!--                    </div>-->
    </div>

</div>
<div class="row modal fade" id="answer-error-box" style="top: 220px;">
    <div class="m-t-60 col-sm-12 col-md-12">
        <div class="answer-detail " >
            <!--                        <img src="../../static/img/qa/icon_错误提示@2x.png" alt="" class="img-48  d-inline-block m-r-10 vertical-mid"/>-->
            <img src="../../static/img/match/bc_lose.png" alt="" class="img-responsive  d-block m-auto"/>
            <br>
            <!--                        <span  class=" d-inline-block vertical-mid">很遗憾，挑战失败！</span>-->
            <div class="btn-m-green m-t-30  m-l-30 retry_btn">
                再试一次
            </div>
        </div>
    </div>
</div>
<script>
    window.onload = function () {
        var i = 0;
        var max = 5;

        var dataContent = <?= $matchAllFlowJson ?>;
        var dataCon=$.toJSON(dataContent);
        var obj = eval( "(" + dataCon + ")" );

            showMsg(0, obj.flow);


    };

    function fight(shakeObj, shakeObj1, obj) {
        var fightInterval = setInterval(function() {
            console.log('pos');
            console.log($(shakeObj1.position().left));
            console.log($(shakeObj.position().left));
            var targetLeft = $(shakeObj1).position().left - 50;
            var hitTargetLeft = targetLeft + 30;
            if (obj.direction == -1) {
                targetLeft = -$(shakeObj1).position().left - 150;
                hitTargetLeft = targetLeft + 150;
            }
            $(shakeObj).animate({
                left: targetLeft,
                width: $(shakeObj1).width()
            }, 200, 'swing', function() {
                clearTimeout(fightInterval);
                targetTop = $(shakeObj1).position().top;
                var hitDiv = '<div class="riv_hit" style="position: absolute; z-index: 99999999; left: ' + hitTargetLeft + 'px; top: ' + targetTop + 'px;"><img width="240" src="../../static/img/match/hit.gif"></div>';
                $(shakeObj1).parent().append(hitDiv);
                $(shakeObj1).parent().find('.riv_hit').animate({
                    opacity: 100
                }, 500, function() {
                    $(this).remove();
                });

                $('#number-floater').css('left', shakeObj1.position().left + 10);
                $('#number-floater').css('font-size', 70);
                var num = obj.hint;
                if (obj.type == 2) {
                    var restNum = obj.restHp - obj.hint;
                    var maxNum = obj.maxHp;
                    var hpRate = restNum / maxNum;
                    var bgColor = 'red';
                    if (hpRate > 0.5) {
                        bgColor = 'green';
                    } else if (hpRate > 0.3) {
                        bgColor = 'yellow';
                    }
                    shakeObj1.parent().find('div').find('div').css({
                        'width': (hpRate * 100) + '%',
                        'background-color': bgColor
                    });
                } else if (obj.type == 1) {
                    num = 'MISS';
                } else {
                    num = '格挡';
                }
                floNumber(num);
                shake(shakeObj1);
                
                if (obj.restHp - obj.hint <= 0) {
                    shakeObj1.parent().find('div').fadeOut();
                    $(shakeObj1).fadeOut();
                }
                setTimeout(function(){
                $(shakeObj).css('left', 0);
                }, 1500);

            });

        }, 500);
        // $(shakeObj1).fadeOut();
        // $(shakeObj).css('left', shakeObjOldLeft);
        // clearInterval(fightInterval);
        // var num = obj.hint;
        // floNumber(num);

        // clearInterval(fightInterval);
        // var num = obj.hint;
        // floNumber(num);
    }

    function shake(shakeObj) {
        $(shakeObj).animate({left: '+=20'}, 200) // 向右移动20px
            .animate({left: '-=20', rotate: -10 + "deg"}, 200) // 返回原位
            .animate({left: '+=10', rotate: 5 + "deg"}, 200) // 稍微右移
            .animate({left: '-=10', rotate: -5 + "deg"}, 200) // 返回原位
            .animate({left: '+=5', rotate: 10 + "deg"}, 200)  // 稍微右移
            .animate({left: '-=5', rotate: 0 + "deg"}, 200); // 返回原位
        // var shakeInterval = setInterval(function() {
        //     $(shakeObj).shake(4, 4, 20);
        // }, 2000);
        // clearInterval(shakeInterval);

    }

    function showMsg(z, obj) {
        $('#msg_' + z).show();
        console.log($('#msg_' + z).length);
        if ($('#msg_' + z).length > 0) {
            $('#msg_' + z).get(0).scrollIntoView();
            console.log(z);
            console.log($('#match_detail_round_' + z).val());
            // var title = '第' + $('#match_detail_title_' + i).val() + '圈';
            // if (i == '999') {
            //     title = '结果';
            // }
            var title = '战斗';
            $('#match_detail_title').html(title);
            // $('#match_detail_round').html($('#match_detail_round_' + i).val());
            // $('#match_detail_desc').html($('#match_detail_round_' + i).val() + ' ' + $('#match_detail_desc_' + i).val());
            // $('#match_detail').modal('show');
            $('#match_detail').show();
            if (obj[z].direction == 1) {
                var shakeObj = $('.avatar_img');
                var shakeObj1 = $('.rival_avatar_img');
                shakeObj.attr('src', obj[z].currentAvatar);
                shakeObj1.attr('src', obj[z].rivalAvatar);
                shakeObj.css('left', '0px');
                shakeObj1.css('left', '50px');
            } else {
                var shakeObj = $('.rival_avatar_img');
                var shakeObj1 = $('.avatar_img');
                shakeObj.attr('src', obj[z].currentAvatar);
                shakeObj1.attr('src', obj[z].rivalAvatar);
                shakeObj1.css('left', '0px');
                shakeObj.css('left', '50px');
            }
            if (obj[z].type < 9) {
                shakeObj1.show();
                shakeObj.show();
                fight(shakeObj, shakeObj1, obj[z]);
            } else {
                showResult(obj[z]);
            }

            setTimeout(function() {showMsg(z+1, obj);}, 2500);
        }
        console.log($('#msg_' + z).length);
        return true;
    }

    function showResult(tobj) {
        // var dobj = $('#match_result');
        // dobj.show();
        if (tobj.fightRet == 1) {
            // dobj.html('You WIN!');
            $('#answer-right-box').modal('show');
        } else {
            // dobj.html('You LOSE!');
            $('#answer-error-box').modal('show');
        }

        // dobj.animate({
        //     width: '60%',
        //     height: '60%',
        //     'font-size': '50px',
        // }, 100);
    }

    function floNumber(num) {
        var duration = 5;
        var height = 0;
        $('#number-floater').html(num);
        $('#number-floater').css('opacity', 100) // 设置初始透明度为0
            .animate({
                top: '-=70',
                // 'font-size': 70,
                opacity: 1 // 渐显
            }, 300)
            .delay(duration) // 延迟随机时间
            .animate({
                top: '-=70',
                opacity: 0, // 渐隐
                'font-size': '-=15'
            }, 200, function() {
                $(this).css('top', height); // 动画完成后重置位置
                $(this).css('font-size', 70);
            });
    }
    
    function showFlow(j, obj) {
        var item = obj[j];
        if (j >= obj.length) {
            $('#top_flow').html('');
            $('#top_flow').hide();
            return true;
        }
        var txt = item.txt;
        $('#top_flow').html(txt);
    }




</script>


