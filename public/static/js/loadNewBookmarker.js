//加载页码
var loadPage = 1;
//加载数量
var loadCount = 21;
//加载标识
var loadSign = 0;
//滚动事件
window.onscroll = function () {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    if (($("body").height() - scrollTop) <= document.documentElement.clientHeight && loadSign == 0) {
        loadSign = 1;
        if (loadPage != 0) {
            //添加加载样式
            $("body").append("<div class='loading' style='padding-top:10px;font-size:12px;color:#767676;text-align: center;'>正在加载中...</div>");
            loadPage++;
            //获取数据
            $.ajax({
                url: '/dm5.ashx?d=' + new Date(),
                dataType: 'json',
                data: { action: "getmorebookmarkers", pageindex: loadPage, pagesize: loadCount, sort: 1 },
                type: 'POST',
                success: function (data) {
                    if (data && data['items']) {
                        var result = '';
                        for (var n = 0; n < data['items'].length; n++) {
                            result += '<li>';
                            result += '<div class="manga-list-2-cover">';
                            result += '<a href="/' + data['items'][n].UrlKey + '/"><img class="manga-list-2-cover-img" src="' + data['items'][n].ShowPicUrlB + '"></a>';
                            result += '<span class="manga-list-2-cover-hover" style="display:none;"></span>';
                            result += '</div>';
                            result += '<p class="manga-list-2-title"><a href="/' + data['items'][n].UrlKey + '/">' + data['items'][n].Title + '</a></p>';
                            if (data['items'][n].LastReadShowName) {
                                result += '<p class="manga-list-2-tip"><a href="/' + data['items'][n].LastReadUrlKey + '/" style="display: unset;">' + data['items'][n].LastReadShowName + '/</a><a href="/' + data['items'][n].LastPartUrl + '/" style="display: unset;">' + data['items'][n].ShowLastPartName + '</a></p>';
                            }
                            else {
                                result += '<p class="manga-list-2-tip"><a style="display: unset;">未读/</a><a href="/' + data['items'][n].LastPartUrl + '/" style="display: unset;">' + data['items'][n].ShowLastPartName + '</a></p>';
                            }
                            result += '</li>';
                        }
                        $(".manga-list ul").append(result);
                        $(".manga-list-2-cover-hover").click(function () {
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
                    } else {
                        loadPage = 0;
                        if ($(".noDataFont").length == 0) {
                            $("body").append("<div class=\"noDataFont\" style=\"padding:10px;font-size:12px;color:#767676;text-align: center;\">主人，下面木有了~</div>");
                        }
                    }
                    $(".loading").remove();
                    loadSign = 0;
                }
            });
        }
        else {
            $(".loading").remove();
            loadSign = 0;
        }
    }
}

String.prototype.startWith = function (s) {
    if (s == null || s == "" || this.length == 0 || s.length > this.length)
        return false;
    if (this.substr(0, s.length) == s)
        return true;
    else
        return false;
    return true;
}

function editBookmarker() {
    $("#btnedit").hide();
    $("#btneditclose").show();
    $(".manga-list-2-cover-hover").show();
    $(".center-main-bottom-btn").show();
}

function editBookmarkerClose() {
    $("#btnedit").show();
    $("#btneditclose").hide();
    $(".manga-list-2-cover-hover").hide();
    $(".center-main-bottom-btn").hide();
}

function deleteBookmarker() {
    if (temparr.length > 0) {
        var uid = DM5_USERID;
        var str = $(temparr).map(function () {
            return '1-' + uid + '-' + this;
        }).get().join(',');
        $.ajax({
            url: 'bookmarkerAction.ashx?d=' + new Date().getTime(),
            dataType: 'json',
            cache: false,
            data: { ids: str, uid: uid, action: "delete" },
            type: 'POST',
            success: function (msg) {
                if (msg.Value === "1") {
                    ShowDialog('删除成功~');
                    var href = window.location.href;
                    window.location.href = href;

                } else {
                    ShowDialog("删除失败！");
                }
            }
        });
    }
    else {
        ShowDialog('请选择要删除的收藏~');
    }
}

var temparr = [];
$(function () {
    $(".manga-list-2-cover-hover").click(function () {
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