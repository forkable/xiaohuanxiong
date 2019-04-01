
$('#btnsave').click(function () {
    var nickname = filterStr($('#nick_name').val());
    if (nickname.length > 12){
        ShowDialog("昵称不能太长");
        return;
    }
    $.post({
        url:'/updateUserinfo',
        data:{nickname: nickname},
        success:function (res) {
            ShowDialog(res.msg);
        }
    })
    setTimeout(function () {
        location.reload();
    },2);
})

function filterStr(str)
{
    var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？%+_]");
    var specialStr = "";
    for(var i=0;i<str.length;i++)
    {
        specialStr += str.substr(i, 1).replace(pattern, '');
    }
    return specialStr;
}