$(document).ready(function () {
    // 禁用封面放大
    $(document).on('mouseenter',
        '.mh-item > .mh-cover .edit-state',
        function (ev) {
            ev.stopPropagation();
            ev.preventDefault();
        });
    // 选择
    $('.mh-item > .mh-cover .edit-state').not('.lock').click(function (ev) {
        ev.stopPropagation();
        ev.preventDefault();
        var $item = $(this)
        $item.hasClass('active') ? $item.removeClass('active') : $item.addClass('active')
        upSelVal()
    });
    // 全选
    $('.box-booklist-edit-head .js_allsel_checkbox').change(function () {
        var isActive = $(this).prop('checked')
        $('.mh-item > .mh-cover .edit-state').each(function () {
            isActive ? $(this).addClass('active') : $(this).removeClass('active')
        })
        upSelVal()
    })

    // 显
    $('.js_edit_booklist_btn').click(function (ev) {
        ev.stopPropagation();
        ev.preventDefault();
        $(this).hide();
        $('.box-booklist-edit-head').show();
        $('span.edit-state').show();
        $('span.edit-state.lock').hide();
    })
    // 隐
    $('.js_esc_booklist_btn').click(function (ev) {
        ev.stopPropagation();
        ev.preventDefault();
        $('.box-booklist-edit-head').hide();
        $('.js_edit_booklist_btn').show();
        $('.mh-item > .mh-cover .edit-state').not('.lock').removeClass('active').hide()
        $('.mh-item > .mh-cover .edit-state.lock').show()
        $('.js_allsel_checkbox').prop('checked', false);
        upSelVal()
    })

    $("#book_del").click(function () {
        var ids = $("#ids").val();
        var uid = $(".pull-right input[type=hidden][name=uid]").val();
        if (ids && ids != "") {
            var arr = ids.split(',');
            if (arr && arr.length > 0) {
                var str = $(arr).map(function () {
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
                            $('.js_esc_booklist_btn').click();
                            yqdm.isrepeat = true;
                            if (yqdm.params.pageindex !== 1 && $('.mh-list li').length > ids.length || yqdm.params.pageindex === 1) {
                                yqdm.search();
                            } else {
                                yqdm.params.pageindex--;
                                yqdm.search();
                            }
                        } else {
                            ShowDialog("删除失败！");
                        }
                    }
                });
            }
        } else {
            alert("请选择要删除的漫画");
        }
    });

    $("#history_del").click(function () {
        var ids = $("#ids").val();
        var uid = $(".pull-right input[type=hidden][name=uid]").val();
        if (ids && ids != "") {
            $.ajax({
                url: '/readHistory.ashx?t=' + new Date().getTime(),
                dataType: 'json',
                cache: false,
                data: { mids: ids, uid: uid, action: "delete" },
                type: 'POST',
                success: function (msg) {
                    if (msg.Value === "1") {
                        ShowDialog('删除成功~');
                        $('.js_esc_booklist_btn').click();
                        yqdm.isrepeat = true;
                        if (yqdm.params.pageindex !== 1 && $('.mh-list li').length > ids.length || yqdm.params.pageindex === 1) {
                            yqdm.search();
                        } else {
                            yqdm.params.pageindex--;
                            yqdm.search();
                        }
                    } else {
                        ShowDialog("删除失败！");
                    }
                }
            });
        } else {
            alert("请选择要删除的漫画");
        }
    });

    // 翻页
    $('.page-pagination ul li a').click(function (e) {
        var index = $(this).data('index');
        if (index > 0) {
            yqdm.changepager(index);
        }
        e.stopPropagation();
        e.preventDefault();
    });

    // 排序
    $('.box-head-sort dd').click(function () {
        yqdm.changesort(this);
    });

    // 搜索
    $('.search-input input[name=title]').keydown(function (e) {
        var title = $.trim($(this).val());
        if (e && e.keyCode === 13 && (!yqdm.oldparams.title && title !== '' || (yqdm.oldparams.title || '') !== title)) {
            yqdm.params.title = title;
            yqdm.params.pageindex = 1;
            yqdm.search();
        }
    });
    $('.search-input button[name=btnsearch]').click(function () {
        var title = $.trim($(this).prev('input[name=title]').val());
        if (!yqdm.oldparams.title && title !== '' || (yqdm.oldparams.title || '') !== title) {
            yqdm.params.title = title;
            yqdm.params.pageindex = 1;
            yqdm.search();
        }
    });
    $('.search-input input[name=title]').bind('input propertychange', function () {
        yqdm.params.title = $.trim($(this).val());
    });
});

function upSelVal() {
    var selIdList = $('.mh-item > .mh-cover .edit-state.active').not('.lock').map(function () {
        return $(this).attr('reg-id');
    }).get();

    $('.js_count_num').text(selIdList.length)
    $('#ids').val(selIdList.join(','))
    if (selIdList.length == $("#hidCount").val()) {
        $('.js_allsel_checkbox').prop('checked', true);
    } else {
        $('.js_allsel_checkbox').prop('checked', false);
    }
}
