// Jamroom Ning Groups Javascript
// @copyright 2003-2011 by Talldude Networks LLC

/**
 * Join a Group
 * @param action string
 * @param group_id int
 * @param gclass string
 * @returns {boolean}
 */
function jrGroupButton(action, group_id, gclass)
{
    if (action == 'join' || action == 'leave' || action == 'cancel') {
        var bid = '#group_button';
        $(bid).attr('disabled', 'disabled');
        var url = core_system_url + '/' + jrGroup_url + '/button/' + action + '/' + group_id + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: url,
            success: function(_msg)
            {
                if (typeof _msg.error != "undefined") {
                    alert(_msg.error);
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
    }
    return false;
}