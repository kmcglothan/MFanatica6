// Jamroom 5 Custom List Params javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * jrListParams_get_views
 */
function jrListParams_get_views(mod)
{
    var url = core_system_url + '/' + jrListParams_url + '/get_views/' + jrE(mod) + '/__ajax=1';
    $.getJSON(url, function(data) {
        if (typeof data.success !== "undefined" && typeof data.success === "object") {
            var id = '#list_view';
            $(id).empty();
            $.each(data.success, function(key, value) {
                $(id).append($('<option></option>').attr('value', key).text(value));
            });
            $(id).removeClass('form_element_disabled').removeAttr('disabled');
        }
    });
}