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

\frontend\assets\Shoph5Asset::register($this);

$this->registerMetaTag([
    'name' => 'referrer',
    'content' => 'no-referrer',
]);
//$this->registerMetaTag([
//    'name' => 'viewport',
//    'content' => 'width=device-width; initial-scale=1.0',
//]);

$this->title = '上传图片';

?>

<input type="hidden" name="user_id" value="<?= $userId ?>">

<div class="w-100 m-auto">
    <div class="p-20 bg-black w-100 m-t-80" style="position: absolute; left: 0px; top: 50px;">
        <div class="w-100 p-30  m-b-10">
            <div class="w-1-0 d-flex">
                <div class="fs-30 bold w-100 text-FF title-box-border2">
                    <div class="btn-m-green m-t-30  m-l-30" style="position: absolute; right: 5px; top: -60px;" id="return_btn">
                        返回
                    </div>
                    <div class="npc-name" style="background-color: #000; color: #DAFC70">
                        上传视频
                    </div>
            <div class="row" id="answer-box" style="margin-top: 50px;">
                地点：
                <select id="poi">
                    <option value="0">请选择</option>
                    <?php
                    if (!empty($poiList)) {
                        foreach ($poiList as $poi) {
                            ?>
                            <option value="<?= $poi['poi_id'] ?>"><?= $poi['poi_name'] ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
                <div class="row" id="answer-box" style="margin-top: 20px;">
                照片：
                    <input type="file" name="fileUpload" style="font-size: 24px;">

            </div>
<!--                    <div class="btn-m-green m-t-30 float-right m-r-20" id="return_btn">
                        返回
                    </div> -->
                </div>
            </div>
            <div class="row" id="answer-box" style="margin-top: 20px;">
                <div class="btn-m-green m-t-30  m-l-30" style="" id="upload_btn">
                    上传
                </div>
            </div>

        </div>
        </div>



</div>

<!-- Loading遮罩层 -->
<div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 9999; justify-content: center; align-items: center;">
    <div style="text-align: center; color: white;">
        <div style="width: 50px; height: 50px; border: 3px solid #f3f3f3; border-top: 3px solid #DAFC70; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
        <div style="font-size: 18px; color: #DAFC70;">正在上传，请稍候...</div>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-right" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        成功
                    </div>
                    <div class="text-center m-t-30" id="right_text">

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<!-- 按钮：用于打开模态框 -->
<div class="modal fade" id="h5-worry" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <span class="close delete-note  m-t-15 m-r-20  fs-24 absolute  z-9999 iconfont iconbtn-guanbi" data-dismiss="modal" style="top: 15px;right: 15px;"></span>
            <div class="p-20-40 relative h5 m-t-30" name="loginStr" style="width: 600px;">
                <div>
                    <div class="fs-36 text-F6 text-center bold">
                        出错了
                    </div>
                    <div class="m-t-40 bg-F5 p-20 fs-26 text-orange border-radius-r-5 border-radius-l-5" id="worry_text">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    window.onload = function () {
        is_enable = true;
        
        // 显示Loading遮罩
        function showLoading() {
            $('#loading-overlay').css('display', 'flex');
        }
        
        // 隐藏Loading遮罩
        function hideLoading() {
            $('#loading-overlay').hide();
        }
        $('#upload_btn').click(function () {
            if (is_enable == false) {
                $.alert('正在上传……');
                return;
            }
            
            var userId = $('input[name="user_id"]').val();
            var poiId = $('#poi').val();
            var file = $('input[name="fileUpload"]')[0].files[0];
            var ebookStory = <?= $ebookStory ?>;

            if (poiId == 0) {
                $.alert('请选择地点');
                return;
            }

            if (!file) {
                $.alert('请选择文件');
                return;
            }
            
            // 显示Loading遮罩
            showLoading();
            is_enable = false;

            var btnObj = $(this);
            btnObj.html('正在上传...');
            btnObj.attr('enable', false);
            
            var formData = new FormData();
            formData.append('fileUpload', file);
            formData.append('user_id', userId);
            formData.append('poi_id', poiId);
            formData.append('ebook_story_id', ebookStory);

            $.ajax({
                url: '/jncity/upload',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // 隐藏Loading遮罩
                    hideLoading();
                    
                    if (response.data.code == 0) {
                        $('#right_text').html('上传成功，正在生成视频……');
                        $('#h5-right').modal('show');
                        btnObj.html('上传');
                        is_enable = true;
                    } else {
                        $('#worry_text').html(response.data.msg);
                        $('#h5-worry').modal('show');
                        btnObj.html('上传');
                        is_enable = true;
                    }
                },
                error: function (xhr, status, error) {
                    // 隐藏Loading遮罩
                    hideLoading();
                    
                    var errorMsg = '上传失败，请重试';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    $('#worry_text').html(errorMsg);
                    $('#h5-worry').modal('show');
                    btnObj.html('上传');
                    is_enable = true;
                }
            });
        });
    };
</script>



