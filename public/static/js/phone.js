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
                url: '/sendcms',
                data: { areacode: areacode, phone: phone },
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
        data:{
            txt_phonecode : $('#txt_phonecode').val(),
            txt_phone : $('#txt_phone').val()
        },
        success(res){
            if (res.err == 0) {
                ShowDialog(res.msg);
                setTimeout(function () {
                    location.href = '/ucenter';
                },2000);
            } else {
                ShowDialog(res.msg);
                setTimeout(function () {
                    location.reload();
                },1000);
            }
        }
    })

})

$('#sub1').click(function () {
    $.post({
        url:'/verifyphone',
        data:{
            txt_phonecode:$('#txt_phonecode').val(),
            txt_phone:$('#txt_phone').val()
        },
        success(res){
            if (res.err == '1'){
                ShowDialog(res.msg);
                setTimeout(function () {
                    location.reload();
                },1000);
            }else {
                location.href = '/bindphone';
            }
        }
    })
})

$('#resetpwd_sub').click(function () {
    if (resetpwd() == true){
        $.post({
            url:'/resetpwd',
            data:{password:$("#txt_password").val()},
            success(res){
                if (res.err == '1'){
                    ShowDialog(res.msg);
                    setTimeout(function () {
                        location.reload();
                    },1000);
                } else {
                    ShowDialog(res.msg);
                    setTimeout(function () {
                        location.href = '/ucenter';
                    },2000);
                }
            }
        })
    }else {
        setTimeout(function () {
            location.reload();
        },1000);
    }
})

function resetpwd()
{
    var regpwd = new RegExp("^[0-9A-Za-z\\-=\\[\\];,./~!@#$%^*()_+}{:?]{6,21}$");
    var $pwd = $("#txt_password");
    var $pwd2 = $("#txt_password2");
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
    }else if (!regpwd.test($pwd.val())) {
        ShowDialog('请输入6位及以上密码');
        $pwd.val('');
        $pwd1.val('');
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