/**
 * Follow discussion toggle
 * @param id discussion id
 */
function jrGroupDiscuss_follow_toggle(id)
{
    var url = core_system_url + '/' + jrGroupDiscuss_url + '/toggle_watch/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.post(url, {
        id: id
    },
    function (rs) {
        if (rs.success) {
            var div = '#discussion_follow_button_' + id + ' span';
            if (rs.following == 'on') {
                $(div).addClass('sprite_icon_hilighted');
            } else {
                $(div).removeClass('sprite_icon_hilighted');
            }
            var bid = $('#discussion_follow_button_' + id);
            var bpr = $(window).width() - bid.offset().left;
            var bpt = bid.offset().top;
            var pid = $('#discussion_follow_drop_' + id);
            pid.appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
            if (pid.is(":visible")) {
                pid.html(rs.tag).fadeOut(2000);
            } else {
                pid.fadeIn().html(rs.tag).fadeOut(2000);
            }
        }
    }, "json");
}

/**
 * Follow discussion toggle
 * @param group_id discussion id
 */
function jrGroupDiscuss_follow_group_toggle(group_id)
{
    var url = core_system_url + '/' + jrGroupDiscuss_url + '/toggle_group_watch/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.post(url, {
        group_id: group_id
    },
    function (rs) {
        if (rs.success) {
            var div = '#discussion_follow_group_button_' + group_id + ' span';
            if (rs.following == 'on') {
                $(div).addClass('sprite_icon_hilighted');
            } else {
                $(div).removeClass('sprite_icon_hilighted');
            }
            var bid = $('#discussion_follow_group_button_' + group_id);
            var bpr = $(window).width() - bid.offset().left;
            var bpt = bid.offset().top;
            var pid = $('#discussion_follow_group_drop_' + group_id);
            pid.appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
            if (pid.is(":visible")) {
                pid.html(rs.tag).fadeOut(2000);
            } else {
                pid.fadeIn().html(rs.tag).fadeOut(2000);
            }
        }
    }, "json");
}

