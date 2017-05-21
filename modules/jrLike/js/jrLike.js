/**
 * @copyright 2012 Talldude Networks, LLC.
 */

var like_in_progress = false;

/**
 * jrLike_action
 */
function jrLike_action(module_url, item_id, action, uid)
{
    if (!like_in_progress) {
        like_in_progress = true;
        var iid = null;
        var pri = $('#like-state-' + uid);
        var lid = '#l' + uid;
        var did = '#d' + uid;
        var lcn = '#lc' + uid;
        var dcn = '#dc' + uid;
        if (action == 'like') {
            iid = lid;
        }
        else if (action == 'dislike') {
            iid = did;
        }
        else {
            alert("Error: Invalid action argument (like/dislike/neutral)");
            like_in_progress = false;
            return false;
        }
        pri.text(action);
        var url = core_system_url + '/' + module_url + '/like_create/' + item_id + '/' + action + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        var old = $(iid + ' img').height();
        $(iid + ' img').animate({height: 0}, 100, function()
        {
            $.ajax({
                type: 'POST',
                cache: false,
                dataType: 'json',
                url: url,
                success: function(r)
                {
                    if (typeof r.OK !== "undefined" && r.OK == 1) {
                        // Success - Change the images
                        $(lid).find('img').attr('src', r.l_src).attr('title', r.l_ttl);
                        $(did).find('img').attr('src', r.d_src).attr('title', r.d_ttl);
                        $(lcn).text(r.l_cnt);
                        $(dcn).text(r.d_cnt);
                    }
                    else if (r.error) {
                        alert(r.error);
                    }
                    $(iid + ' img').animate({height: old}, 50);
                    setTimeout(function()
                    {
                        like_in_progress = false;
                    }, 3000);
                },
                error: function()
                {
                    alert('an error was encountered saving the like - please try again');
                    like_in_progress = false;
                }
            });
        });
    }
    like_in_progress = false;
    return false;
}

/**
 * Get users that have liked / disliked an item
 */
function jrLike_get_like_users(e, mod, iid, type, uid)
{
    if (Number($(e).text()) > 0) {
        var url = core_system_url + '/' + jrLike_url + '/get_like_users/m=' + jrE(mod) + '/i=' + Number(iid) + '/t=' + type + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'GET',
            cache: false,
            dataType: 'html',
            url: url,
            success: function(r)
            {
                $('#liker_list_' + uid).html(r);
                $('#likers-' + uid).modal();
            },
            error: function()
            {
                alert('an error was encountered getting the user list - please try again');
            }
        });
    }
}
