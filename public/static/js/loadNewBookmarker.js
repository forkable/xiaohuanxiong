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
    var temparr = $('.manga-list-2-cover-hover.active');
    if (temparr.length > 0) {
        var str = $(temparr).map(function () {
            return $(this).attr('mid');
        }).get().join(',');
        console.log(str);
        $.ajax({
            url: '/delfavors',
            data: {ids: str},
            type: 'POST',
            dataType:'json',
            success:function(res){
                if (res.err === "0") {
                    ShowDialog(res.msg);
                } else {
                    ShowDialog(res.msg);
                }
            }
        })
    } else {
        ShowDialog('请选择要删除的收藏~');
    }
    setTimeout(function () {
        location.reload();
    },2);
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
        } else {
            $(this).addClass("active");
            if ($.inArray(mid, temparr) == -1) {
                temparr.push(mid);
            }
        }
    });
});