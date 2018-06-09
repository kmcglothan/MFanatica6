// Jamroom Chained Select Module javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * jrChainedSelect_get
 */
function jrChainedSelect_get(field, name, lvl, key, value)
{
    var id = '#' + field + '_' + lvl;
    if (typeof value === "undefined" && lvl == 2) {
        key = $('#'+ field +'_0').val() +'|'+ key;
    }
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: core_system_url + '/' + jrChainedSelect_url + '/get/' + name + '/' + lvl + '/' + key + '/__ajax=1',
        success: function(_msg) {
            if (typeof _msg.error != "undefined") {
                if (_msg.error == 'no_data') {
                    $(id).addClass('form_element_disabled').attr('disabled', 'disabled').find('option').remove().end();
                    return true;
                }
                else {
                    alert(_msg.error);
                }
            }
            else if (_msg.ok == '1' && _msg.value != '[]') {
                $(id).removeClass('form_element_disabled').removeAttr('disabled').find('option').remove().end();
                $.each(_msg.value, function(key, value) {
                    $(id).append($('<option></option>').attr('value', key).text(value));
                });
                if (typeof value != "undefined") {
                    $(id).val(value);
                }
                if (lvl == 1) {
                    $('#' + field +'_2').val('-');
                }
            }
            return true;
        },
        error: function() {
            alert('a system level error was encountered submitting the request - please try again (2)');
            return false;
        }
    });
    return false;
}

function jrChainedSelect_load(name, lvl, key)
{
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: core_system_url + '/' + jrChainedSelect_url + '/get/' + name + '/' + lvl + '/' + key + '/nd=1/__ajax=1',
        success: function(_msg) {
            var id = '#set_options';
            $(id).val('');
            if (typeof _msg.error != "undefined") {
                if (_msg.error == 'no_data') {
                    return true;
                }
                else {
                    alert(_msg.error);
                }
            }
            else if (_msg.ok == '1' && _msg.value != '[]') {
                $(id).val(_msg.value);
            }
            return true;
        },
        error: function() {
            alert('a system level error was encountered submitting the request - please try again (2)');
            return false;
        }
    });
    return false;
}
