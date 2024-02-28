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

\frontend\assets\Phoneh5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

//$this->title = $qa['topic'];

?>
<div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
  返回
</div>

<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="qa_id" value="<?= $qaId ?>">
<input type="hidden" name="answer_type" value="<?= $defAnswerType ?>">
<input type="hidden" name="default_answer_type" value="<?= $defAnswerType ?>">
<input type="hidden" name="after_close" value="0">

   <div class="toast-box hide">
       <div class="toast">
           Toast提示
       </div>
   </div>
   <audio controls id="audio_right" class="hide">
       <source src="../../static/audio/wait_call.mp3" type="audio/mpeg">
       您的浏览器不支持 audio 元素。
   </audio>
   <audio controls id="audio_wrong" class="hide">
       <source src="" type="audio/mpeg">
       您的浏览器不支持 audio 元素。
   </audio>


    <div class="keypad-top">
        <div class="keypad-shop-info">
            <span class="shop-name">请输入手机号</span>
        </div>
        <label class="inputlabel" id="keypadNum" type="text"></label>
    </div>
    <div class="keypadinfo" id="keypad-close">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td class="" ></td>
                <td class="keypad-close">
                    <a href="javascript:return false;">
                        <label class="keypad-m close">
                            <img src="../../static/img/close.png">
                        </label>

                    </a>
                </td>
                <td class="" ></td>
            </tr>
        </table>
    </div>

    <div class="keypadinfo" id="keypad-open">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td class="keypadnum table-left"><label class="keypad-m">1</label></td>
                <td class="keypadnum"><label class="keypad-m">2</label></td>
                <td class="keypadnum table-right"><label class="keypad-m">3</label></td>
            </tr>
            <tr>
                <td class="keypadnum table-left"><label class="keypad-m">4</label></td>
                <td class="keypadnum"><label class="keypad-m">5</label></td>
                <td class="keypadnum table-right"><label class="keypad-m">6</label></td>
            </tr>
            <tr>
                <td class="keypadnum table-left"><label class="keypad-m">7</label></td>
                <td class="keypadnum"><label class="keypad-m">8</label></td>
                <td class="keypadnum table-right"><label class="keypad-m">9</label></td>
            </tr>
            <tr>
                <td class="keypadnum" ></td>
                <td class="keypadnum" ><label class="keypad-m">0</label></td>
                <td id="keypad-return">
                    <div class="keybord-return"></div>
                </td>
            </tr>
            <tr>
                <td class="" ></td>
                <td class="keypad">
                    <a href="javascript:return false;">
                        <label class="keypad-m">
                            <img src="../../static/img/phone.png">
                        </label>
                        <label class="keypad-m close hide">
                            <img src="../../static/img/close.png">
                        </label>

                    </a>
                </td>
                <td class="" ></td>
            </tr>
        </table>
    </div>