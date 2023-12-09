$(function () {

    $("#play").click(function (){
        var audio_right=$("#audio_right")[0];
        var audio_wrong=$("#audio_wrong")[0];
        // $.alert('请检查网络');
        audio_right.play();

    });

    var loadingHtml=`
        <div class="col-12 blank-box loading-box"  >
        <!--<i class="iconfont iconicon-shuaxin fs-60 text-44" id="loading"></i>-->
        
        <div class="m-t-60  m-b-60" align="center">
           <img src="../../static/image/loading-1.gif" class="m-auto w-100px m-b-20"/><br>
            数据加载中~
        </div>
    </div>`;

    //下拉刷新职位
    //滚动控制搜索框显示
    var windowHeight = $(window).height();
    window.onscroll = function(){
        showTop()
        const el = document.scrollingElement || document.documentElement;
        var scrollHeight = $(document).height();

        var pageName=$("input[name='page-name']").val()

        if ((el.scrollTop +windowHeight >= scrollHeight)&&pageName=='h5-list') {
            console.log("滑到底部上滑");
            h5JobLoadMore();
        }
    }

    $(".h5-list-next-btn-box").click(function () {
        h5JobLoadMore()

    })
    function showTop(){
        const el = document.scrollingElement || document.documentElement;
        console.log(el.scrollTop)
        el.scrollTop > 100 ? $('.toTop').css({
            opacity: 1
        }) : $('.toTop').css({
            opacity: 0
        });
    }


    function h5JobLoadMore() {
        if(!$("input[name='page-name']").hasClass('disable')){
            $(".loading-box").remove();
            var url=window.location.pathname+window.location.search
            var page=parseInt($("input[name='page']").val())+1;
            var pageSum=parseInt($("input[name='page_sum']").val());
            var htmlLast=`<div class="text-center fs-24 text-99 p-20 last-text"> 已到底</div>`
            if(pageSum==0){

            }
            else if(page>pageSum){
                $(".last-text,.h5-list-next-btn-box").remove();
                $("#job-list-box").append(htmlLast);

            }else if(page<=pageSum){
                $("#job-list-box").append(loadingHtml);
                $("input[name='page-name']").addClass('disable')
                $.ajax({
                    type: "GET", //用POST方式传输
                    dataType: "html", //数据格式:JSON
                    url:"/h5/job_list_api"+window.location.search+"&page="+page,
                    success: function (result, status){
                        $("input[name='page-name']").removeClass('disable')
                        $(".loading-box").remove();

                        $("input[name='page']").remove();
                        $("input[name='page_sum']").remove();
                        $(".h5-list-next-btn-box").remove();
                        $("#job-list-box").append(result);

                        var pageNew=parseInt($("input[name='page']").val()+1);
                        var pageSumNew=parseInt($("input[name='page-sum']").val());
                        if(pageNew>=pageSumNew){
                            $(".last-text,.h5-list-next-btn-box").remove();
                            $("#job-list-box ul").append(htmlLast);
                        }
                        $(".h5-list-next-btn-box").unbind().click(function () {
                            h5JobLoadMore();
                        })
                    },
                    error : function(res){
                        $("input[name='page-name']").removeClass('disable')
                        $.alert('请检查网络');
                        $(".loading-box").remove();
                    }
                });

            }
        }


    }

});