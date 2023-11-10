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


\frontend\assets\Qah5Asset::register($this);


$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '灵镜新世界-下载';

?>

<div class="w-100 m-auto">

  <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
    <div class="w-100 p-30  m-b-10">
      <div class="w-1-0 d-flex">
        <div class="fs-30 bold w-100 text-FF title-box-border ">
            <img src="/static/image/explorer.png">
        </div>
      </div>

    </div>
  </div>

</div>

</div>

