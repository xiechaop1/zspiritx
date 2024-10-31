$(function () {
    //关闭提示信息
    var record_tag = 0;
    $('#record').click(function() {
        if (record_tag == 0) {
            var params = {
                'gameFlag': "startMicRec",
            }
            var data = $.toJSON(params);
            Unity.call(data);
            record_tag = 1;
            console.log($(this).find('img').attr('src'));
            $(this).find('img').attr('src', '../../static/img/match/mic_re_g1.png');
            // $(this).val('Stop');
        } else {
            var params = {
                'gameFlag': "stopMicRec",
                'recArgs_source':"doc",
                'recArgs_type':'asr',
            }
            var data = $.toJSON(params);
            Unity.call(data);
            record_tag = 0;
            $(this).find('img').attr('src', '../../static/img/match/mic_s_g1.png');
            // $(this).val('Record');
        }
    });



})

function getTalkBase(data, objId, micIconId) {
    var dataContent = data;
    var dataCon = $.toJSON(dataContent);
    var voiceObj = eval("(" + dataCon + ")");//转换后的JSON对象

    $('#' + objId).val(voiceObj.data.text);
    $('#' + micIconId).attr('src', '../../static/img/match/mic_g1.png');
}