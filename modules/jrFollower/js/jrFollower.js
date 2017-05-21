// Jamroom jrFollower Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@talldude.net

/**
 * jrFollowProfile
 */
function jrFollowProfile(id, pid)
{
    var bid = '#' + id;
    $(bid).attr('disabled', 'disabled');
    var url = core_system_url + '/' + jrFollower_url + '/follow/' + pid + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(m)
        {
            if (typeof m.error != "undefined") {
                alert('a system level error was encountered submitting the request - please try again');
            }
            else {
                window.location.reload();
            }
            return true;
        },
        error: function()
        {
            alert('a system level error was encountered submitting the request - please try again');
            return false;
        }
    });
    return false;
}

/**
 * jrUnFollowProfile
 */
function jrUnFollowProfile(id, pid)
{
    var bid = '#' + id;
    $(bid).attr('disabled', 'disabled');
    var url = core_system_url + '/' + jrFollower_url + '/unfollow/' + pid + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(m)
        {
            if (typeof m.error != "undefined") {
                alert('a system level error was encountered submitting the request - please try again');
            }
            else {
                window.location.reload();
            }
            return true;
        },
        error: function()
        {
            alert('a system level error was encountered submitting the request - please try again');
            return false;
        }
    });
    return false;
}

/**
 * Get profiles followed by a user
 */
function jrFollower_get_followed()
{
    var url = core_system_url + '/' + jrFollower_url + '/get_followed/__ajax=1';
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(r)
        {
            if (typeof r.error != "undefined") {
                alert(r.error);
            }
            else {
                var e = $('.follow_entry');
                if (e.length > 0) {
                    e.each(function()
                    {
                        var i = $(this).attr('data-id');
                        if (typeof r.following[i] !== "undefined") {
                            if (r.following[i] == 1) {
                                $('#a' + i).show();
                            }
                            else {
                                $('#p' + i).show();
                            }
                        }
                        else {
                            $('#f' + i).show();
                        }
                    });
                }
            }
            return true;
        },
        error: function()
        {
            alert('a system level error was encountered submitting the request - please try again');
            return false;
        }
    });
}
