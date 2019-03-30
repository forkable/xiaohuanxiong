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

var temparr = [];
$(function () {
    $(".buy-manga-cover-hover").click(function () {
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

function clearReadhistory() {

}

function deleteReadhistory()
{
    var temparr = $('.buy-manga-cover-hover.active');
    if (temparr.length > 0) {
        var str = $(temparr).map(function () {
            return $(this).attr('mid');
        }).get().join(',');
        console.log(str);
        $.ajax({
            url: '/delhistory',
            data: {keys: str},
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