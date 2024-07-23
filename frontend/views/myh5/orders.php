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

$this->title = '我的订单';

?>
<style>
  .text-line-through {
    text-decoration: line-through;
    color: yellow;
  }
  a {
    color: yellow;
  }
</style>


<div class="w-100 m-auto">

  <div class="p-20 bg-black w-100 m-t-80" style="position: relative; left: 0px; top: 0px;">
    <div class="w-100 p-30  m-b-10">
      <div class="w-1-0 d-flex">
        <div class="fs-30 bold w-100 text-FF title-box-border ">
          <div class="npc-name">
            订单
          </div>

            <?php
            foreach ($orderList as $order) {
                switch ($order->item_type) {
                    case \common\models\Order::ITEM_TYPE_PACKAGE:
                        $itemName = !empty($order->shopWare->ware_name) ? $order->shopWare->ware_name : '';
                        $coverImage = !empty($order->shopWare->icon) ? \common\helpers\Attachment::completeUrl($order->shopWare->icon, true) : '';
                        $itemDesc = !empty($order->shopWare->intro) ? mb_substr($order->shopWare->intro, 30) : '';
                        break;
                    case \common\models\Order::ITEM_TYPE_STORY:
                    default:
                        $itemName = !empty($order->story->title) ? $order->story->title : '';
                        $coverImage = !empty($order->story->cover_image) ? \common\helpers\Attachment::completeUrl($order->story->cover_image, true) : '';
                        $itemDesc = !empty($order->story->desc) ? mb_substr($order->story->desc, 30) : '';
                        break;
                }
            ?>
          <div class="row" id="answer-box">
            <div class="m-t-30 col-sm-12 col-md-12">
              <div class="answer-border">
                  <div style="clear: both; margin: 3px;">
                    <div align="left" style="font-size: 18px; color: #e0c46c;">订单号：<?= $order->order_no ?></div>
                  </div>
                  <div style="clear: both; margin: 3px;">
                      <div style="float: left; width: 100px; margin: 5px;"><img src="<?= $coverImage ?>" style="width: 100px; height: 100px; float: left; margin-right: 10px;"></div>
                      <div style="float: left; font-size: 24px; margin: 5px;">
                          <div><?= $itemName ?></div>
                          <div style="color: #6c6c6c"><?= $itemDesc ?></div>
                      </div>
                  </div>
                    <div style="clear: both; margin: 3px;">
                        <div align="right" style="font-size: 18px; color: #e0c46c;">时间：<?= Date('Y-m-d H:i:s', $order->created_at) ?>&nbsp;
                            <span style="color: <?= $order->order_status == \common\models\Order::ORDER_STATUS_COMPLETED
                            || $order->order_status == \common\models\Order::ORDER_STATUS_PAIED ? 'green' : '#a83800' ?>">
                                <?= !empty(\common\models\Order::$orderStatus[$order->order_status]) ? \common\models\Order::$orderStatus[$order->order_status] : ' - ' ?>
                            </span>

                        </div>
                    </div>
                <!-- <input class="form-check-input" type="radio" name="knowledge" value="' . $item->id . '" id="legal_person_yes_' . $item->id . '" > -->
<!--                <label class="form-check-label fs-30 answer-btn">-->
<!--                  <span ><a href="https://zspiritx.oss-cn-beijing.aliyuncs.com/doc/zspiritx_useragreement.docx">用户协议</a></span>-->
<!--                </label>-->
              </div>
            </div>
          </div>
            <?php
            }
            ?>

        </div>
      </div>

    </div>
  </div>

</div>

</div>


