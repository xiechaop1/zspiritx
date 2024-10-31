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

$this->title = '消息';

?>
<style>
    .background-darkgrey {
        width: 120px;
        background-color: rgba(31, 38, 40, 0.8);
    }
</style>
<div class="w-100 m-auto">

    <div class="p-20 bg-black">
        <div class="w-100 p-30  m-b-10" style="text-align: center;">
            <div class="btn-m-green background-darkgrey m-t-30 m-r-20 menubtn" id="btn1" type="1" style="display: none;">
                <img src="../../static/img/index/dialog_btn_g.png" width="60" height="60">
            </div>
            <div class="btn-m-green background-darkgrey m-t-30 m-r-20 menubtn" id="btn2" type="2" style="display: none;">
                <img src="../../static/img/index/mic_btn_g.png" width="45" height="60">
            </div>
            <div class="btn-m-green background-darkgrey m-t-30 m-r-20 menubtn" id="btn3" type="3" style="display: none;">
                <img src="../../static/img/index/camera_btn_g.png" width="60" height="60">
            </div>
            <div class="btn-m-green background-darkgrey m-t-30 m-r-20 menubtn" id="btn4" type="4" style="display: none;">
                <img src="../../static/img/index/home_btn_g.png" width="60" height="60">
            </div>

        </div>
        </div>

    </div>

</div>
<script>
    window.onload = function () {
        $('#btn1').slideDown(300);
        $('#btn2').slideDown(500);
        $('#btn3').slideDown(700);
        $('#btn4').slideDown(900);

        $('.menubtn').click(function() {
            var answerType = $(this).attr('type');
            var params = {
                'WebViewOff':1,
                'AnswerType':answerType
            }
            var data=$.toJSON(params);
            Unity.call(data);
        });
    }
</script>