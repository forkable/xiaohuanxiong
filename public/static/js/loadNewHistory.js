//加载页码
var loadPage = 1;
//加载数量
var loadCount = 10;
//加载标识
var loadSign = 0;
//滚动事件
window.onscroll = function () {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    if (($("body").height() - scrollTop) <= document.documentElement.clientHeight && loadSign == 0 ) {
        loadSign = 1;
        if(loadPage != 0){
            //添加加载样式
            $("body").append("<div class='loading' style='padding-top:10px;font-size:12px;color:#767676;text-align: center;'>正在加载中...</div>");
            loadPage++;
            //获取数据
            $.ajax({
                url: 'pagerdata.ashx?d=' + new Date(),
                dataType: 'json',
                data: { t: 6, pageindex: loadPage },
                type: 'POST',
                success: function (data) {
                    if(data.length > 0){
                        var result = '';
                        for(var n = 0;n < data.length;n++){
                            result += '<div class="buy-manga" id="buy-manga' + data[n].Mid+'">';
                            result += '<div class="buy-manga-cover">';
                            result += '<img src="' + data[n].CoverUrl + '">';
                            result += '<a class="buy-manga-cover-hover" href="javascript:void(0);" style="display:none;"></a>';
                            result += '</div>';
                            result += '<div class="buy-manga-info">';
                            result += '<a href="/' + data[n].MangaUrl + '/?from=' + DM5_CURRENTURL + '"  class="readlink" mid="' + data[n].Mid + '"><p class="buy-manga-title">' + data[n].MName + '</p></a>';
                            result += '<p class="buy-manga-author"></p>';
                            result += '<a href="/' + data[n].LastPartUrl + '/" class="readlink" mid="' + data[n].Mid + '"><p class="buy-manga-new">更新至' + data[n].ShowLastChapter + '</p></a>';
                            result += '<a class="buy-manga-right-a readlink" href="/' + data[n].LastReadUrl + '/" mid="' + data[n].Mid + '">';
                            result += '<img class="buy-manga-right-img" src="' + host + 'images/mobile/buy-manga-right.png">';
                            result += '<p class="buy-manga-right-title">续看</p>';
                            result += '</a>';
                            result += '</div>';
                            result += '</div>';
                        }
                        $(".bg-gray").append(result);
                        $(".buy-manga-cover-hover").click(function () {
                            var mid = $(this).attr("mid");
                            if ($(this).hasClass("active")) {
                                $(this).removeClass("active");
                                if ($.inArray(mid, temparr) != -1) {
                                    temparr.splice($.inArray(mid, temparr), 1);
                                }
                            }
                            else {
                                $(this).addClass("active");
                                if ($.inArray(mid, temparr) == -1) {
                                    temparr.push(mid);
                                }
                            }
                        });
                        $(".readlink").click(function () {
                            if (isedit) {
                                var mid = $(this).attr("mid");
                                var item = $("#buy-manga" + mid + " .buy-manga-cover-hover");
                                if (item.hasClass("active")) {
                                    item.removeClass("active");
                                    if ($.inArray(mid, temparr) != -1) {
                                        temparr.splice($.inArray(mid, temparr), 1);
                                    }
                                }
                                else {
                                    item.addClass("active");
                                    if ($.inArray(mid, temparr) == -1) {
                                        temparr.push(mid);
                                    }
                                }
                                return false;
                            }
                            return true;
                        });
                    }
                    else{
                        loadPage = 0;
                        if($(".noDataFont").length == 0){
                            $(".bg-gray").append("<div class='noDataFont' style=\"padding:10px;font-size:12px;color:#767676;text-align: center;\">主人，下面木有了~</div>");
                        }
                    }
                    $(".loading").remove();
                    loadSign = 0;
                }
            });
        }
        else{
            $(".loading").remove();
            loadSign = 0;
        }
    }
}


var isedit = false;
function editReadhistory() {
    $("#btnedit").hide();
    $("#btneditclose").show();
    $(".buy-manga-cover-hover").show();
    $(".center-main-bottom-btn").show();
    isedit = true;
}

function editReadhistoryClose() {
    $("#btnedit").show();
    $("#btneditclose").hide();
    $(".buy-manga-cover-hover").hide();
    $(".center-main-bottom-btn").hide();
    isedit = false;
}

function clearReadhistory() {
    var userid = DM5_USERID;
    $.post("/readHistory.ashx?t=" + new Date().getTime(), { uid: userid, action: "clearall" }, function (result) {
        var data = JSON.parse(result);
        if (data && data["Value"] === "1") {
            ShowDialog('清空成功~');
            window.location.reload();
        } else {
            ShowDialog("清除记录失败，请重试");
        }
    });
}

function deleteReadhistory()
{
    if (temparr.length > 0) {
        var uid = DM5_USERID;
        var str = temparr.join(',');
        $.ajax({
            url: '/readHistory.ashx?t=' + new Date().getTime(),
            dataType: 'json',
            cache: false,
            data: { mids: str, uid: uid, action: "delete" },
            type: 'POST',
            success: function (msg) {
                if (msg.Value === "1") {
                    ShowDialog('删除成功~');
                    window.location.reload();

                } else {
                    ShowDialog("删除失败！");
                }
            }
        });
    }
    else {
        ShowDialog('请选择要删除的历史~');
    }
}


var temparr = [];
$(function () {
    $(".readlink").click(function () {
        if (isedit)
        {
            var mid = $(this).attr("mid");
            var item = $("#buy-manga" + mid + " .buy-manga-cover-hover");
            if (item.hasClass("active")) {
                item.removeClass("active");
                if ($.inArray(mid, temparr) != -1) {
                    temparr.splice($.inArray(mid, temparr), 1);
                }
            }
            else {
                item.addClass("active");
                if ($.inArray(mid, temparr) == -1) {
                    temparr.push(mid);
                }
            }
            return false;
        }
        return true;
    });
    $(".buy-manga-cover-hover").click(function () {
        var mid = $(this).attr("mid");
        if ($(this).hasClass("active")) {
            $(this).removeClass("active");
            if ($.inArray(mid, temparr) != -1) {
                temparr.splice($.inArray(mid, temparr), 1);
            }
        }
        else {
            $(this).addClass("active");
            if ($.inArray(mid, temparr) == -1) {
                temparr.push(mid);
            }
        }
    });
});