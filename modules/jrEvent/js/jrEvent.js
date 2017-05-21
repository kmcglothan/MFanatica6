// Jamroom jrEvent Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Paul Asher - paul@jamroom.net

/**
 * jrEventAttending
 */
function jrEventAttend(event_id)
{
    var url = core_system_url +'/'+ jrEvent_url +'/attend/'+ event_id +'/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg) {
            if (typeof _msg.error != "undefined") {
                alert(_msg.error);
            }
            else {
                window.location.reload();
            }
            return true;
        },
        error: function() {
            alert('a system level error was encountered submitting the request - please try again');
            return false;
        }
    });
}
