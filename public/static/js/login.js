var regpwd = new RegExp("^[0-9A-Za-z\\-=\\[\\];,./~!@#$%^*()_+}{:?]{6,21}$");
var regemail = new RegExp('^([\\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$');

/**
 * 验证form表单数据
 * @param {any} action
 */
function verifyform() {
    var $username = $("#txt_username");
    var $pwd = $("#txt_password");
    var $phone = $("txt_phone");
    var $phonecode = $("txt_phonecode");
    var $pwd = $("#txt_password");
    var $pwd1 = $("#txt_password2");
    var $tip = $(parent + " .account-login-form .tip");
    if ($phone.length>0) {
        if ($.trim($phone.val()) === "")
        {
            $phone.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
            ShowDialog("手机号码不填可是不行的哦~");
            return false;
        }
        if (!$phonecode || $.trim($phonecode.val()) === "") {
            $phonecode.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
            ShowDialog("短信验证码不填可是不行的哦~");
            return false;
        }
    }
    else {
        if (!$username || $.trim($username.val()) === "") {
            ShowDialog("账号信息不填可是不行的哦~");
            $username.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
            return false;
        }
    }
    if (!$pwd || $.trim($pwd.val()) === "") {
        ShowDialog("密码不填可是不行的哦~");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    if ($pwd1.length > 0 && $.trim($pwd1.val()) === "") {
        ShowDialog("请再次输入密码");
        $pwd1.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" });
        return false;
    }
    if ($pwd1.length > 0 && $.trim($pwd.val()) !== $.trim($pwd1.val())) {
        $tip.text("两次输入的密码不一致，请重新输入");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" }).val("");
        $pwd1.val("");
        return false;
    }
    if (!regpwd.test($pwd.val())) {
        ShowDialog("密码由6-20位字母、数字和字符组成");
        $pwd.focus().css({ outlineWidth: 1, outlineColor: "#fd113a" }).val("");
        if ($pwd1.length > 0) {
            $pwd1.val("");
        }
        return false;
    }
    return true;
}
