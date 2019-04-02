$(function () {
    $(".btnphonecodeget").click(function () {
        if ($('.btnphonecodeget').attr('isok') == 1){
            var phone = $(".txt_phone").val();
            if (phone == '') {
                ShowDialog("手机不填可是不行的哦~");
                return false;
            }
            var areacode = $(".txt_areacode").val();
            $.ajax({
                url: '/sendcode',
                data: { areacode: areacode, phone: phone },
                async: false,
                error: function (msg) {
                },
                success: function (res) {
                    ShowDialog(res.msg)
                    $('.btnphonecodeget').attr('isok',0);
                    $('.line-container-btn').attr('isok',1);
                    startTime();
                }
            });
        }

    });
});

$('#sub').click(function () {
    $.post({
        url:'/bindphone',
        data:$('form').serialize(),
        success(res){
            if (res.err == 0) {
                ShowDialog(res.msg);
                setTimeout(function () {
                    location.href = '/ucenter';
                },2);
            } else {
                ShowDialog(res.msg);
            }
        }
    })

})

$('#sub1').click(function () {
    if ($('.line-container-btn').attr('isok') == 1) {
        $.get({
            url:'/verifycode',
            data:{
                code:$('#txt_phonecode').val(),
                phone:$('#txt_phone').val()
            },
            success(res){
                if (res == '0'){
                    ShowDialog('验证码错误');
                }else {
                    location.href = '/bindphone';
                }
            }
        })
    } else {
        ShowDialog('验证码不正确');
    }
})

$('#resetpwd_sub').click(function () {
    console.log(resetpwd());
    if (resetpwd() == true){
        $.post({
            url:'/recovery',
            data:$('form').serialize(),
            success(res){
                if (res.err == '1'){
                    ShowDialog(res.msg);
                } else {
                    ShowDialog(res.msg);
                    setTimeout(function () {
                        location.href = '/ucenter';
                    },2);
                }
            }
        })
    }
})

function resetpwd()
{
    var $pwd = $("#txt_password");
    var $pwd2 = $("#txt_password2");
    var $phonecode = $(".txt_phonecode");
    if (!$pwd || $.trim($pwd.val()) === "") {
        ShowDialog("密码不填可是不行的哦~");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    else if (!$pwd2 || $.trim($pwd2.val()) === "") {
        ShowDialog("新密码不填可是不行的哦~");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    else if ($pwd.val() != $pwd2.val()) {
        ShowDialog("两次密码输入不一致~");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        $pwd2.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    else if (!$phonecode || $.trim($phonecode.val()) === "") {
        $phonecode.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        ShowDialog("短信验证码不填可是不行的哦~");
        return false;
    }
    return  true;
}

function startTime() {
    var index = 60;
    var timer = setInterval(function () {
        index--;
        if (index == 0) {
            clearInterval(timer);
            $('.btnphonecodeget').attr('isok',1);
            $('.btnphonecodeget').text('获取验证码');
        }
        else {
            $('.btnphonecodeget').text('重新获取(' + index + ')');
        }
    }, 1000);
}