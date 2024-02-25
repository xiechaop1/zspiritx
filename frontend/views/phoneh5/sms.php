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

\frontend\assets\Phoneotherh5Asset::register($this);

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
<input type="hidden" name="user_id" value="<?= $userId ?>">
<input type="hidden" name="story_id" value="<?= $storyId ?>">
<input type="hidden" name="qa_id" value="<?= $qaId ?>">
<input type="hidden" name="answer_type" value="<?= empty($forgot) ? '0' : '1' ?>">

<div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
  返回
</div>

<div style="font-size: 32px;">
  <?= $label ?>
</div>
<?php
if (empty($forgot) || $forgot == 0) {
?>
<div class="label2">
  <?= $label2 ?>
</div>
  <form id="input_form" method="post">
  <div class="input_item">
    <div>姓名（拼音）：</div>
    <div><input type="text" name="username" id="username" size="30"></div>
  </div>
  <div class="input_item">
    <div>密码：</div>
    <div><input type="password" name="password" id="password">&nbsp; <a href="javascript:void(0);" id="reset_password">忘记密码</a></div>
  </div>
  <div class="input_item">
    <div><input type="submit" value="登录"></div>
  </div>
  </form>
  <?php
} else {
  if (!empty($smsContents)) {
    foreach ($smsContents as $sms) {
  ?>
    <div class="sms_item">
      <div class="sms_title">
      <div class="sms_phone_num"><?= $sms['phone_num'] ?>
<!--        郑雨曦-->
      </div>
      <div class="sms_date"><?= $sms['sms_date'] ?>
<!--        2021-05-06-->
      </div>
      </div>
      <div class="sms_content">
      <div class="sms_detail"><?= $sms['sms_content'] ?>
<!--        大伟叔叔，我是郑烛光的女儿郑雨曦，我爸爸想给我买小提琴买不起，无奈才去拿的。我知道触犯了法律，麻烦大伟叔叔可以多多照顾一下我爸爸吗？-->
      </div>
      </div>
    </div>

  <?php
    }
  }
}
?>
<div>

</div>
