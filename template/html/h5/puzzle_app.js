$(function () {
    //关闭提示信息
    $(".close-note").on('click',function () {
        var me=$(this);
        me.closest('.note').remove();
    });

    //打开modal
    $(".open-modal").on('click',function () {
        var me=$(this);
        var dataId=me.attr('data-id');
        $("#"+dataId).modal('show');
    })

    //页面重新加载
    $(".window-reload").on('click',function () {
        window.location.reload()
    })


    //判断是否答对
    $("input[name='answer']").click(function () {
        submitAnswer($(this));
    });

    $(".verify_code input[name='answer_txt']").change(function () {
        var v_selects = $("input[name='answer_txt']");
        for (i = 0; i < v_selects.length; i++) {
            if (v_selects[i].value == '') {
                return false;
            }
        }
        submitAnswer($(this));
    });

    function submitAnswer(thisObj) {
        var that=$("#answer-info");
        var qa_id=that.attr("data-qa");
        var qa_type=that.attr("data-type");
        var story_id=that.attr("data-story");
        var user_id=$("input[name='user_id']").val();
        var session_id=$("input[name='session_id']").val();
        var session_stage_id=$("input[name='session_stage_id']").val();
        var begin_ts=$("input[name='begin_ts']").val();
        var v_ture=that.attr("data-value");
        var v_detail=that.attr("data-detail");
        if (qa_type == 1 || qa_type == 2 || qa_type == 3 || qa_type == 4) {
            var v_select = $("input[name='answer']:checked").val();
        } else if (qa_type == 7) {
            var v_select = $("input[name='answer_txt']").val();
        } else if (qa_type == 9) {
            var v_select1 = $("input[name='answer_txt']").val();
            var v_select2 = '';
            // var v_select2 = $("#answer-border-response").html();
            var v_select = v_select2 + v_select1;
        } else if (qa_type == 8) {
            var v_selects = $("input[name='answer_txt']");
            var v_select = '';
            for (var i = 0; i < v_selects.length; i++) {
                v_select += v_selects[i].value;
            }
        }
        // $("#answer-box").hide();
        if(v_select==null){
            $("#h5-null").modal('show');
        }


        if(v_select!=null){
            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/qa/add_user_answer',
                data:{
                    user_id:user_id,
                    qa_id:qa_id,
                    answer:v_select,
                    story_id:story_id,
                    session_id:session_id,
                    session_stage_id:session_stage_id,
                    begin_ts:begin_ts
                },
                onload: function (data) {
                    $('#answer-border-response').html('处理中……');
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    $.alert("网络异常，请检查网络情况");
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    //console.log("ajax请求成功:"+data.toString())

                    //audio 素材
                    var audio_right=$("#audio_right")[0];
                    var audio_wrong=$("#audio_wrong")[0];

                    //新消息获取成功
                    if(obj["code"]==200){
                        if (qa_type == 9) {
                            console.log(obj);
                            // $.alert(obj.data.msg);
                            var htmlObj = $('#answer-border-response');
                            if (htmlObj.html() == '等待提问……') {
                                htmlObj.html('');
                            }
                            htmlObj.html(htmlObj.html() + '<br>' + '小灵语：' + obj.data.msg);
                            if (obj.data.voice != undefined) {
                                var audio_voice=$("#audio_voice")[0];
                                audio_voice.src = obj.data.voice;
                                audio_voice.play();
                            }
                            $("input[name='answer_txt']").val('');
                            return false;
                        }

                        if(v_ture==v_select){
                            $("#answer-box").hide();
                            $("#answer-right-box").removeClass('hide');
                            audio_right.play();

                            if (obj.data.score.score != undefined) {
                                var score_text = "+" + obj.data.score.score + "枚";
                                if (obj.data.score.addition > 0) {
                                    score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                                }
                                $("#gold_score").html(score_text);
                            }

                            setTimeout(function (){
                                // Unity.call('WebViewOff&TrueAnswer');
                                var params = {
                                    'WebViewOff':1,
                                    'AnswerType':1
                                }
                                var data=$.toJSON(params);
                                Unity.call(data);
                            },2000);
                        }
                        else{
                            $("#answer-box").hide();
                            $("#answer-error-box").removeClass('hide');
                            // $("#h5-worry").modal('show');
                            audio_wrong.play();
                            setTimeout(function (){
                                // Unity.call('WebViewOff&FalseAnswer');
                                // var params = {
                                //     'WebViewOff':1,
                                //     'AnswerType':2
                                // }
                                // var data=$.toJSON(params);
                                // Unity.call(data);
                                location.reload();
                            },2000);
                        }
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
    };

    var height = $(window).height();
    $("#myCarousel .item,#banner .item").css('height',height+'px')

    $("#dialog_return_btn").click(function (){
        var tar_id = $(this).attr('target_id');
        var dialog = $('#' + tar_id);
        // dialog.hide();
        dialog.modal('hide');

        var need_refresh = $(this).attr('need_refresh');
        if (need_refresh == 1) {
            location.reload();
        }
    });

    $("#return_btn").click(function (){
        var params = {
            'WebViewOff':1
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $("#qa_return_btn").click(function (){
        // Unity.call('WebViewOff&FalseAnswer');
        var rtnAnswerType = $('#rtn_answer_type').val();
        var params = {
            'WebViewOff':1,
            'AnswerType':rtnAnswerType
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $("#guide_confirm_return_btn").click(function (){
        // Unity.call('WebViewOff&TrueAnswer');

        var params = {
            'AnswerType':1,
            'WebViewOff':1
        }
        var data=$.toJSON(params);
        Unity.call(data);
    });

    $("#msg_return_btn").click(function (){
        // Unity.call('WebViewOff&TrueAnswer');

        var answerType = $(this).attr('answer_type');
        if (answerType == undefined) {

            var params = {
                'WebViewOff': 1
            }
        } else {
            var params = {
                'WebViewOff': 1,
                'AnswerType': answerType
            }
        }
        // console.log(params);
        var data=$.toJSON(params);
        Unity.call(data);
    });


    $(".keyboard").click(function (){
        var obj = $(this);
        var val = obj.attr('val');
        var input_obj = obj.parent().parent().find("input[NAME='answer_txt']");
        if (val != 'DELETE') {
            input_obj.val(input_obj.val() + val);
        } else {
            input_obj.val(input_obj.val().slice(0, -1));
        }
    });

    $(".puzzle_image_item").click(function() {
        if ($(this).attr('lock') == 1) {
            return false;
        }
        if ($(this).hasClass('choosen')) {
            $(this).removeClass('choosen');
        } else {
            $('.puzzle_image_item').removeClass('choosen');
            $(this).addClass('choosen');
        }
    });

    $(".v_puzzle_image_keyboard").click(function() {
        var obj = $(this);
        var val = obj.attr('val');

        // console.log($('.puzzle_image_item'));
        // var ct = 0;
        var tobj = $('.puzzle_image_item.choosen');
        console.log(tobj);
        if (tobj.length > 0) {
            tobj.attr('right_val', obj.attr('right_val'));
            tobj.html(obj.html());
        } else {
            $('.puzzle_image_item').each(function () {
                if ($(this).html().trim() == '') {
                    $(this).attr('right_val', obj.attr('right_val'));
                    // $(this).attr('val', ct);
                    $(this).html(obj.html());
                    return false;
                }
                // ct++;
            });
        }

        var chk = checkPuzzle();
        console.log(chk);
        // var target_obj = '';
        // console.log(target_obj);
        // console.log($(this).html());
        // target_obj.html($(this).html());
        return false;

        // var input_obj = obj.parent().parent().find("input[NAME='answer_txt']");
        // if (val != 'DELETE') {
        //     var j=0;
        //     // 定义一个数组
        //     var list = new Array();
        //     for (i=0; i<input_obj.length; i++) {
        //         if ($(input_obj[i]).val() =="" ) {
        //             $(input_obj[i]).val(val);
        //             return true;
        //             // list.push(i);
        //             // j++;
        //             // if (j == val.length) {
        //             //     console.log(list);
        //             //     for (k=0; k<list.length; k++) {
        //             //         $(input_obj[list[k]]).val(val[k]);
        //             //     }
        //             //     return true;
        //             // }
        //         } else {
        //             j = 0;
        //             list = [];
        //         }
        //     }
        // } else {
        //     for (i=input_obj.length - 1; i>=0; i--) {
        //         if ($(input_obj[i]).val() !="" ) {
        //             $(input_obj[i]).val('');
        //             return true;
        //         }
        //     }
        // }
    });

    function checkPuzzle() {
        var chk = 1;
        $('.puzzle_image_item').each(function(){
           if ($(this).attr('right_val') != $(this).attr('val')
               || $(this).attr('right_val') == ""
            || $(this).html().trim() == ""
           ) {
               chk = 0;
               return false;
           }
        });
        console.log(chk);
        if (chk == 1) {
            var that=$("#answer-info");
            var qa_id=$("input[name='qa_id']").val();
            var user_id=$("input[name='user_id']").val();
            var session_id=$("input[name='session_id']").val();
            var session_stage_id=$("input[name='session_stage_id']").val();
            var begin_ts=$("input[name='begin_ts']").val();
            console.log('success');
            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/qa/add_user_answer',
                data:{
                    user_id:user_id,
                    qa_id:qa_id,
                    answer:'True',
                    story_id:story_id,
                    session_id:session_id,
                    session_stage_id:session_stage_id,
                    begin_ts:begin_ts
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    $.alert("网络异常，请检查网络情况");
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    //console.log("ajax请求成功:"+data.toString())
                    //新消息获取成功
                    if(obj["code"]==200){
                        $('.puzzle_image_item').unbind('click');
                        $('.puzzle_image_item').removeClass('choosen');
                        $('.puzzle_image_item').addClass('puzzle_item_end');
                        if (obj.data.score.score != undefined) {
                            var score_text = "+" + obj.data.score.score + "枚";
                            if (obj.data.score.addition > 0) {
                                score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                            }
                            $("#gold_score").html(score_text);
                        }
                        // var audio_right=$("#audio_right")[0];
                        // audo_right.play();
                        $("#answer-box").removeClass('hide');
                        $("#answer-right-box").removeClass('hide');

                        // $("#h5-right").modal('show');
                        // setTimeout(function (){
                        //     // Unity.call('WebViewOff&TrueAnswer');
                        //     var params = {
                        //         'WebViewOff':1,
                        //         'AnswerType':1
                        //     }
                        //     var data=$.toJSON(params);
                        //     Unity.call(data);
                        // },3000)
                        setTimeout(function () {
                            // Unity.call('WebViewOff&TrueAnswer');
                            // var params = {
                            //     'WebViewOff':1,
                            //     'AnswerType':1
                            // }
                            // var data=$.toJSON(params);
                            // Unity.call(data);
                            $("#answer-right-box").addClass('hide');
                        }, 4000);
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
        return (chk == 1);
    }

    $(".v_keyboard").click(function (){
        var obj = $(this);
        var val = obj.attr('val');
        var input_obj = obj.parent().parent().find("input[NAME='answer_txt']");

        if (val != 'DELETE') {
            var j=0;
            // 定义一个数组
            var list = new Array();
            for (i=0; i<input_obj.length; i++) {
                if ($(input_obj[i]).val() =="" ) {
                    $(input_obj[i]).val(val);
                    return true;
                    // list.push(i);
                    // j++;
                    // if (j == val.length) {
                    //     console.log(list);
                    //     for (k=0; k<list.length; k++) {
                    //         $(input_obj[list[k]]).val(val[k]);
                    //     }
                    //     return true;
                    // }
                } else {
                    j = 0;
                    list = [];
                }
            }
        } else {
            for (i=input_obj.length - 1; i>=0; i--) {
                if ($(input_obj[i]).val() !="" ) {
                    $(input_obj[i]).val('');
                    return true;
                }
            }
        }
    });

    $(".v_s_keyboard").click(function (){
        var sudoku_current = $('#sudoku_current').val();
        $('.v_s_keyboard_choosen').removeClass('v_s_keyboard_choosen');
        $('.DELETE_v_s_keyboard_choosen').removeClass('DELETE_v_s_keyboard_choosen');
        var thisVal = $(this).attr('val');
        if (sudoku_current != thisVal) {
            $('#sudoku_current').val(thisVal);
            console.log(thisVal);
            if (thisVal != 'DELETE') {
                $(this).addClass('v_s_keyboard_choosen');
            } else {
                $(this).addClass('DELETE_v_s_keyboard_choosen');
            }
        } else {
            $('#sudoku_current').val('');
            // $(this).addClass('v_s_keyboard_choosen');
        }
    });

    $(".puzzle_sudoku_item").click(function() {
        var sudokuCurrent = $('#sudoku_current').val();
        var sudokuSize = $('#sudoku_size').val();
        var readOnly = $(this).attr('ro');

        var user_id=$("input[name='user_id']").val();
        var story_id=$("input[name='story_id']").val();
        var session_id=$("input[name='session_id']").val();
        var session_stage_id=$("input[name='session_stage_id']").val();
        var qa_id=$("input[name='qa_id']").val();
        var begin_ts=$("input[name='begin_ts']").val();

        console.log(readOnly);

        if (readOnly == '1') {
            return false;
        }

        if (sudokuCurrent == '') {
            return false;
        }
        if (sudokuCurrent == 'DELETE') {
            $(this).html(' ');
        } else {
            $(this).html(sudokuCurrent);
        }

        if (isValidSudoku(sudokuSize) == true) {
            console.log('success');
            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/qa/add_user_answer',
                data:{
                    user_id:user_id,
                    qa_id:qa_id,
                    answer:'True',
                    story_id:story_id,
                    session_id:session_id,
                    session_stage_id:session_stage_id,
                    begin_ts:begin_ts
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    $.alert("网络异常，请检查网络情况");
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    //console.log("ajax请求成功:"+data.toString())
                    //新消息获取成功
                    if(obj["code"]==200){
                        $('.puzzle_check').unbind('click');
                        $('.puzzle_check').removeClass('puzzle_item');
                        $('.puzzle_check').addClass('puzzle_item_end');
                        if (obj.data.score.score != undefined) {
                            var score_text = "+" + obj.data.score.score + "枚";
                            if (obj.data.score.addition > 0) {
                                score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                            }
                            $("#gold_score").html(score_text);
                        }
                        // var audio_right=$("#audio_right")[0];
                        // audo_right.play();
                        $("#answer-box").removeClass('hide');
                        $("#answer-right-box").removeClass('hide');

                        // $("#h5-right").modal('show');
                        // setTimeout(function (){
                        //     // Unity.call('WebViewOff&TrueAnswer');
                        //     var params = {
                        //         'WebViewOff':1,
                        //         'AnswerType':1
                        //     }
                        //     var data=$.toJSON(params);
                        //     Unity.call(data);
                        // },3000)
                        setTimeout(function () {
                            // Unity.call('WebViewOff&TrueAnswer');
                            // var params = {
                            //     'WebViewOff':1,
                            //     'AnswerType':1
                            // }
                            // var data=$.toJSON(params);
                            // Unity.call(data);
                            $("#answer-right-box").addClass('hide');
                        }, 4000);
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });
        }
    });

    var isValidSudoku = function(size) {
        const [row, col, boxes] = [{}, {}, {}];

        for (let i = 0; i < size; i++) {
            for (let j=0; j < size; j++) {
                var num = $('#puzzle_sudoku_' + i + '_' + j).html();
                num = num.replace(/\s*/g, "");
                if (num == '') {
                    return false;
                }
                const boxIndex = parseInt(i/3) * 3 + parseInt(j/3);
                if (row[i + '-' + num]
                    || col[j + '-' + num]
                    || (size == 9 && boxes[boxIndex + '-' + num])
                ) {
                    return false;
                }

                row[i + '-' + num] = true;
                col[j + '-' + num] = true;
                boxes[boxIndex + '-' + num] = true;
            }
        }
        return true;
    }

    function htmlEncode(html) {return $("<div>").text(html).html()};
    function htmIDecode(encodedHtml) {return $("<div>").html(encodedHtml).text();}

    $("#logout_btn").click(function() {
        var unityVersion = $('#unity_version').val();
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/user/logout',
            data:{},
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                //新消息获取成功
                if(obj["code"]==200){
                    location.href='/passport/web_login?unity_version=' + unityVersion;
                }
                //新消息获取失败
                else{
                    alert(obj.msg)
                }

            }
        });

    });

    $("#delete_btn").click(function() {
        var unityVersion = $('#unity_version').val();
        if (!window.confirm('您确认注销您的账号吗？注销以后，数据将全部丢失！')) {
            return false;
        }
        $.ajax({
            type: "GET", //用POST方式传输
            dataType: "json", //数据格式:JSON
            async: false,
            url: '/user/delete',
            data:{},
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                $.alert("网络异常，请检查网络情况");
            },
            success: function (data, status){
                var dataContent=data;
                var dataCon=$.toJSON(dataContent);
                var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                console.log(obj);
                
                //新消息获取成功
                if(obj["code"]==200){
                    location.href='/passport/web_login?unity_version' + unityVersion;
                }
                //新消息获取失败
                else{
                    alert(obj.msg);
                    location.href='/passport/web_login?unity_version' + unityVersion;
                }

            }
        });

    });

    var oldI = -1;
    var tarI = -1;
    $('.puzzle_item').click(function() {
        // console.log($(this).attr('i'));
        var thisI = $(this).attr('i');

        if (oldI == '-1') {
            oldI = thisI;
            $(this).removeClass('puzzle_item');
            $(this).addClass('puzzle_item_active');
            console.log(oldI);
        } else {
            var oldDiv = $('#puzzle_image_' + oldI);
            var newDiv = $('#puzzle_image_' + thisI);

            oldDiv.removeClass('puzzle_item_active');
            oldDiv.addClass('puzzle_item');

            var tempDiv = newDiv.html();
            var tempDivId = newDiv.attr('id');
            var tempDivI = newDiv.attr('i');
            newDiv.html(oldDiv.html());
            newDiv.attr('id', oldDiv.attr('id'));
            newDiv.attr('i', oldDiv.attr('i'));
            oldDiv.html(tempDiv);
            oldDiv.attr('id', tempDivId);
            oldDiv.attr('i', tempDivI);
            oldI = -1;
            // var oldImg = $('#puzzle_image_'+oldI).attr('src');
            // var
            //     // .find('img').attr('src');
            // // console.log(newImg);
            // var tempI = thisI;
            // var newI = oldI;
            // oldI = tempI;

            // $(this).removeClass('puzzle_item_active');
            // $(this).addClass('puzzle_item');


            var items = $('.puzzle_check');
            var ct = 0;
            var right = 1;
            items.each(function () {
                var eachI = $(this).attr('i');
                console.log('eachI:' + eachI);
                console.log('ct:' + ct);
                if (eachI != ct) {
                    right = 0;
                    return false;
                }
                ct++;
            });
            console.log('right = ' + right);
            if (right == 1) {
                console.log(right);
                var user_id=$("input[name='user_id']").val();
                var session_id=$("input[name='session_id']").val();
                var session_stage_id=$("input[name='session_stage_id']").val();
                var qa_id=$("input[name='qa_id']").val();
                var story_id=$("input[name='story_id']").val();
                var begin_ts=$("input[name='begin_ts']").val();

                $.ajax({
                    type: "GET", //用POST方式传输
                    dataType: "json", //数据格式:JSON
                    async: false,
                    url: '/qa/add_user_answer',
                    data:{
                        user_id:user_id,
                        qa_id:qa_id,
                        answer:'True',
                        story_id:story_id,
                        session_id:session_id,
                        session_stage_id:session_stage_id,
                        begin_ts:begin_ts
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                        $.alert("网络异常，请检查网络情况");
                    },
                    success: function (data, status){
                        var dataContent=data;
                        var dataCon=$.toJSON(dataContent);
                        var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                        //console.log("ajax请求成功:"+data.toString())
                        //新消息获取成功
                        if(obj["code"]==200){
                                $('.puzzle_check').unbind('click');
                                $('.puzzle_check').removeClass('puzzle_item');
                                $('.puzzle_check').addClass('puzzle_item_end');
                            if (obj.data.score.score != undefined) {
                                var score_text = "+" + obj.data.score.score + "枚";
                                if (obj.data.score.addition > 0) {
                                    score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                                }
                                $("#gold_score").html(score_text);
                            }
                            // var audio_right=$("#audio_right")[0];
                            // audo_right.play();
                                $("#answer-box").removeClass('hide');
                                $("#answer-right-box").removeClass('hide');

                                // $("#h5-right").modal('show');
                                // setTimeout(function (){
                                //     // Unity.call('WebViewOff&TrueAnswer');
                                //     var params = {
                                //         'WebViewOff':1,
                                //         'AnswerType':1
                                //     }
                                //     var data=$.toJSON(params);
                                //     Unity.call(data);
                                // },3000)
                                setTimeout(function () {
                                    // Unity.call('WebViewOff&TrueAnswer');
                                    // var params = {
                                    //     'WebViewOff':1,
                                    //     'AnswerType':1
                                    // }
                                    // var data=$.toJSON(params);
                                    // Unity.call(data);
                                    $("#answer-right-box").addClass('hide');
                                }, 4000);
                        }
                        //新消息获取失败
                        else{
                            $.alert(obj.msg)
                        }

                    }
                });

            }
        }
        // for (itemI in items) {
        //     // if (items[i].attr('i')) {
        //     //     tarI = i;
        //     // }
        //     console.log(itemI);
        //     // console.log($(i).attr('i'));
        // }
    });

    $('.puzzle_word_item').click(function() {
        // console.log($(this).attr('i'));
        // var thisI = $(this).attr('i');

        $(this).toggleClass('puzzle_word_item_active');

        // $(this).removeClass('puzzle_word_item');
        // $(this).addClass('puzzle_word_item_active');

        var st_answer = $('#st_answer').val();
        var ct = 0;
        var right = 0;
        var st_right = st_answer.length;
        var items = $('.puzzle_word_item_active');
        var ret = 0;
        items.each(function() {
            var eachVal = $(this).attr('val');
            if (eachVal == 0) {
                right = 0;
                return false;
            }
            ret += parseInt(eachVal);
            console.log(ret);
            if (ret == st_right) {
                right = 1;
            }
        });
        console.log('right = ' + right);
        if (right == 1) {
            console.log(right);
            var user_id=$("input[name='user_id']").val();
            var session_id=$("input[name='session_id']").val();
            var session_stage_id=$("input[name='session_stage_id']").val();
            var qa_id=$("input[name='qa_id']").val();
            var story_id=$("input[name='story_id']").val();
            var begin_ts=$("input[name='begin_ts']").val();

            $.ajax({
                type: "GET", //用POST方式传输
                dataType: "json", //数据格式:JSON
                async: false,
                url: '/qa/add_user_answer',
                data:{
                    user_id:user_id,
                    qa_id:qa_id,
                    answer:st_answer,
                    story_id:story_id,
                    session_id:session_id,
                    session_stage_id:session_stage_id,
                    begin_ts:begin_ts
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
                    $.alert("网络异常，请检查网络情况");
                },
                success: function (data, status){
                    var dataContent=data;
                    var dataCon=$.toJSON(dataContent);
                    var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
                    //console.log("ajax请求成功:"+data.toString())
                    //新消息获取成功
                    if(obj["code"]==200){
                        $('.puzzle_item').unbind('click');
                        if (obj.data.score.score != undefined) {
                            var score_text = "+" + obj.data.score.score + "枚";
                            if (obj.data.score.addition > 0) {
                                score_text = score_text + "（奖：" + obj.data.score.addition + "枚）";
                            }
                            $("#gold_score").html(score_text);
                        }
                        $("#answer-box").removeClass('hide');
                        $("#answer-right-box").removeClass('hide');
                        // var audio_right=$("#audio_right")[0];
                        // audo_right.play();
                        // $("#h5-right").modal('show');
                        setTimeout(function (){
                            // Unity.call('WebViewOff&TrueAnswer');
                            var params = {
                                'WebViewOff':1,
                                'AnswerType':1
                            }
                            var data=$.toJSON(params);
                            Unity.call(data);
                        },3000);
                    }
                    //新消息获取失败
                    else{
                        $.alert(obj.msg)
                    }

                }
            });


            // setTimeout(function (){
            //     // Unity.call('WebViewOff&TrueAnswer');
            //     // var params = {
            //     //     'WebViewOff':1,
            //     //     'AnswerType':1
            //     // }
            //     // var data=$.toJSON(params);
            //     // Unity.call(data);
            //     $("#answer-right-box").addClass('hide');
            // }, 4000);
        }
        // for (itemI in items) {
        //     // if (items[i].attr('i')) {
        //     //     tarI = i;
        //     // }
        //     console.log(itemI);
        //     // console.log($(i).attr('i'));
        // }
    });

})