// Jamroom Invite Module javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * jrInvite_load
 */
function jrInvite_load(module)
{
    if (module != '-') {
        $.ajax({
            type: 'GET',
            cache: false,
            dataType: 'json',
            url: core_system_url + '/' + jrInvite_url + '/load/' + module + '/__ajax=1',
            success: function(_msg) {
                var id = '#invite_item_id';
                $(id).empty();
                if (typeof _msg.error != "undefined") {
                    if (_msg.error == 'no_data') {
                        return true;
                    }
                    else {
                        alert(_msg.error);
                    }
                }
                else if (_msg.ok == '1' && _msg.value != '[]') {
                    $.each(_msg.value, function( key, value) {
                        $(id).append('<option value="' + key + '">' + value + '</option>');
                    });
                }
                return true;
            },
            error: function() {
                alert('a system level error was encountered submitting the request - please try again');
                return false;
            }
        });
    }
}
