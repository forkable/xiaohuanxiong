$(function () {
    $(".btnphonecodeget").click(function () {
        if ($('.btnphonecodeget').attr('isok') == 1){
            var phone = $(".txt_phone").val();
            if (phone == '') {
                ShowDialog("手机不填可是不行的哦~");
                return false;
            }
            var code = $(".txt_phonecode").val();
            var areacode = $(".txt_areacode").val();
            $.ajax({
                url: '/verifycode',
                data: { code: code, areacode: areacode, phone: phone },
                async: false,
                error: function (msg) {
                },
                success: function (res) {
                    ShowDialog(res.msg)
                    $('.btnphonecodeget').attr('isok',0);
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
            ShowDialog(res.msg);
        }
    })
    setTimeout(function () {
        location.href = '/ucenter';
    },2);
})

function resetpwdbyphone()
{
    var $pwd = $(".txt_reg_password");
    var $pwd2 = $(".txt_reg_password2");
    var $hint = $('.toast');
    $hint.text("");
    var $phonecode = $(".txt_phonecode");
    if (!$pwd || $.trim($pwd.val()) === "") {
        $hint.text("密码不填可是不行的哦~");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    else if (!$pwd2 || $.trim($pwd2.val()) === "") {
        $hint.text("新密码不填可是不行的哦~");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    else if ($pwd.val() != $pwd2.val()) {
        $hint.text("两次密码输入不一致~");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        $pwd2.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    else if (!$phonecode || $.trim($phonecode.val()) === "") {
        $phonecode.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        $hint.text("短信验证码不填可是不行的哦~");
        return false;
    }
}

function forgetpwdbyphone() {
    var $username = $(".right input[type=text][name=txt_username]");
    var $hint = $('.toast');
    $hint.text("");
    if (!$username || $.trim($username.val()) === "") {
        $tip.text("账号信息不填可是不行的哦~");
        $username.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    } 
    if (!checkcode(3)) {
        $hint.text("请点击下方图片，旋转至正确方向~");
        return false;
    }
    return true;
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