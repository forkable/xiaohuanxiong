var regpwd = new RegExp("^[0-9A-Za-z\\-=\\[\\];,./~!@#$%^*()_+}{:?]{6,21}$");
var regemail = new RegExp('^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$');

$(function () {
    $("#btnLogin").click(function () {
        verifyform(1);
        return false;
    });
    //回车事件
    $(".modal-wrap .login-modal").bind("keyup", function (event) {
        var e = event || window.event;
        var active = document.activeElement;
        if (active && active.nodeName === 'INPUT' && active.name === 'code') {
            if ($(active).data("status") !== 1) {
                if (e && e.keyCode === 13) {
                    verifyform(1);
                }
            }
            $(active).data("status", 0);
        } else {
            if (e && e.keyCode === 13) {
                verifyform(1);
            }
        }
    });

    $(".modal-wrap .login-modal input[name=txt_code]").bind("compositionstart", function () {
        $(this).data("status", 1);
    });

    $('.rotate-background').each(function () {

        $(this).css('background-image', 'url(/image3.ashx?t=' + Date.parse(new Date()) + ')');

        $(this).click(function () {
            var value1 = parseInt($(this).find('input').val());
            value1++;
            $(this).css('background-position', $(this).css('background-position').split(' ')[0] + ' -' + ((value1 % 4) * 76) + 'px');
            $(this).find('input').val(value1);
        });
    });

    $('.rotate-refresh').click(function () {
        var imageUrl = '/image3.ashx?t=' + Date.parse(new Date());
        $(this).parent().parent().find('.rotate-background').each(function () {
            $(this).css('background-image', 'url(' + imageUrl + ')');
            $(this).css('background-position', $(this).css('background-position').split(' ')[0] + ' 0px');
            $(this).find('input').val(0);
        });
    });
});

/**
 * 验证form表单数据
 * @param {any} action
 */
function verifyform(action) {
    var parent = action === 1 ? ".login-modal" : ".right";
    var $username = $(parent + " input[type=text][name=" + (action !== 3 ? "username" : "txt_reg_name") + "]");
    var $pwd = $(parent + " input[type=password][name=" + (action !== 3 ? "password" : "txt_reg_password") + "]");
    var $phone = $(parent + " input[type=text][name=" + "txt_phone" + "]");
    var $phonecode = $(parent + " input[type=text][name=" + "txt_phonecode" + "]");
    var $pwd1 = $(parent + " input[type=password][name=txt_reg_password2]");
    var $tip = $(parent + " .account-login-form .tip");
    if ($phone.length>0) {
        if ($.trim($phone.val()) === "")
        {
            $phone.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
            $tip.text("手机号码不填可是不行的哦~");
            return false;
        }
        if (!$phonecode || $.trim($phonecode.val()) === "") {
            $phonecode.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
            $tip.text("短信验证码不填可是不行的哦~");
            return false;
        }
    }
    else {
        if (!$username || $.trim($username.val()) === "") {
            $tip.text("账号信息不填可是不行的哦~");
            $username.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
            return false;
        } else if (action === 3 && !regemail.test($username.val())) {
            $tip.text("亲，看起来不像邮箱呢~");
            $username.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
            return false;
        }
    }
    if (!$pwd || $.trim($pwd.val()) === "") {
        $tip.text("密码不填可是不行的哦~");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    if ($pwd1.length > 0 && $.trim($pwd1.val()) === "") {
        $tip.text("请再次输入密码");
        $pwd1.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    if ($pwd1.length > 0 && $.trim($pwd.val()) !== $.trim($pwd1.val())) {
        $tip.text("两次输入的密码不一致，请重新输入");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" }).val("");
        $pwd1.val("");
        return false;
    }
    if (!regpwd.test($pwd.val()) && action === 3) {
        $tip.text("密码由6-20位字母、数字和字符组成");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" }).val("");
        if ($pwd1.length > 0) {
            $pwd1.val("");
        }
        return false;
    }
    if (!checkcode(action)) {
        $tip.text("验证码错误");
        return false;
    }
    return true;
}
