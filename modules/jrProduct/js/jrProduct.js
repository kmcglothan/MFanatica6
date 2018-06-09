// Jamroom Comment Module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

function jrProduct_show_cat_fields(cat_id, prod_id)
{
    var url = core_system_url + '/' + jrProduct_url + '/get_cat_fields/' + cat_id + '/' + prod_id + '/__ajax=1';
    $.getJSON(url, function(data) {
        if (typeof data.success !== "undefined" && typeof data.success === "object") {
            $.each(data.success, function(k, v) {
                $("#cat_fields_label_" + k).html(v.label);
                $("#cat_fields_detail_" + k).html(v.detail);
            });
        }
        else if (typeof data.error !== "undefined" && typeof data.success === "object") {
            jrCore_alert(data.error);
        }
    });
}

