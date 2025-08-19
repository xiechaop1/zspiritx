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
            <div class="row" id="answer-box" style="margin-top: 20px; z-index: 0;">
                <div class="btn-m-green m-t-30  m-l-30" style="" id="upload_btn">
                    上传
                </div>
            </div>

        </div>
        </div>



</div>

<!-- Loading遮罩层 -->
<div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 99999; justify-content: center; align-items: center;">
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

/* 确保遮罩层覆盖所有元素 */
#loading-overlay,
#success-overlay,
#error-overlay {
    pointer-events: auto;
}

/* 遮罩层显示时禁用页面滚动 */
body.modal-open {
    overflow: hidden;
}
</style>

<!-- 成功提示遮罩层 -->
<div id="success-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 99999; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; border-radius: 10px; padding: 30px; max-width: 500px; width: 90%; position: relative;">
        <span class="close-btn" style="position: absolute; top: 15px; right: 15px; font-size: 24px; cursor: pointer; color: #999;" onclick="closeSuccessModal()">×</span>
        <div style="text-align: center;">
            <div style="font-size: 36px; color: #333; font-weight: bold; margin-bottom: 20px;">
                成功
            </div>
            <div id="right_text" style="margin-top: 20px; color: #666;">
            </div>
        </div>
    </div>
</div>

<!-- 错误提示遮罩层 -->
<div id="error-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 99999; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; border-radius: 10px; padding: 30px; max-width: 500px; width: 90%; position: relative;">
        <span class="close-btn" style="position: absolute; top: 15px; right: 15px; font-size: 24px; cursor: pointer; color: #999;" onclick="closeErrorModal()">×</span>
        <div style="text-align: center;">
            <div style="font-size: 36px; color: #333; font-weight: bold; margin-bottom: 20px;">
                出错了
            </div>
            <div id="worry_text" style="margin-top: 20px; padding: 20px; background: #f5f5f5; border-radius: 5px; color: #ff6600; font-size: 16px;">
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
            $('body').addClass('modal-open');
        }
        
        // 隐藏Loading遮罩
        function hideLoading() {
            $('#loading-overlay').hide();
            $('body').removeClass('modal-open');
        }
        
        // 显示成功提示遮罩
        function showSuccessModal(message) {
            $('#right_text').html(message);
            $('#success-overlay').css('display', 'flex');
            $('body').addClass('modal-open');
        }
        
        // 显示错误提示遮罩
        function showErrorModal(message) {
            $('#worry_text').html(message);
            $('#error-overlay').css('display', 'flex');
            $('body').addClass('modal-open');
        }
        
        // 关闭成功提示遮罩
        window.closeSuccessModal = function() {
            $('#success-overlay').hide();
            $('body').removeClass('modal-open');
        }
        
        // 关闭错误提示遮罩
        window.closeErrorModal = function() {
            $('#error-overlay').hide();
            $('body').removeClass('modal-open');
        }
        
        // 点击遮罩层关闭对话框
        $(document).on('click', '#success-overlay', function(e) {
            if (e.target === this) {
                closeSuccessModal();
            }
        });
        
        $(document).on('click', '#error-overlay', function(e) {
            if (e.target === this) {
                closeErrorModal();
            }
        });
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
                        showSuccessModal('上传成功，正在生成视频……');
                        btnObj.html('上传');
                        is_enable = true;
                    } else {
                        showErrorModal(response.data.msg);
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
                    
                    showErrorModal(errorMsg);
                    btnObj.html('上传');
                    is_enable = true;
                }
            });
        });
    };
</script>



