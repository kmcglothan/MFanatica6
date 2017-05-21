// Jamroom Graph Module Javascript
// @copyright 2003-2014 by Talldude Networks LLC

/**
 * Display a graph in a modal window
 * @returns {boolean}
 */
function jrGraph_modal_graph(id, module_url, name, modal, element)
{
    // Get any extra posted args on our URL
    var u = core_system_url + '/' + module_url + '/graph/' + name + '/__ajax=1';
    if (typeof element !== "undefined" && element !== null) {
        var i = $(element).attr('href');
        if (typeof i !== "undefined" && i !== null && i.length > 0) {
            u = i;
        }
    }
    $(id).modal();
    $.get(u, function(r) {
        if (r.indexOf('plothover') !== -1) {
            $(id).html(r);
        }
        else {
            $.modal.close();
            alert(r);
        }
    });
    return false;
}