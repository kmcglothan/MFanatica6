// Jamroom Document Module Javascript
// @copyright 2003-2015 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Submit the docs search form
 */
function jrDocs_search_submit()
{
    $('#doc_search_submit').attr("disabled", "disabled").addClass('form_button_disabled');
    $('#form_submit_indicator').show(300, function () {
        setTimeout(function () {
            $('#doc_search_form').submit();
        }, 500);
    });
}

/**
 * Add a new section to an existing document
 */
function jrDocs_create_section(profile_id, item_id, uid, order)
{
    var pid = '#new_section_' + uid;
    if ($(pid).is(":visible")) {
        jrDocs_hide();
    }
    else {
        if (isNaN(parseFloat(order))) {
            order = 'end';
        }
        $('.overlay').hide();
        var bid = $('#new_section_button_' + uid);
        var bc  = bid.width() / 2;
        var bpr = $(window).width() - (bid.offset().left + bc);
        var bpt = bid.offset().top;
        $(pid).appendTo('body').css({'position': 'absolute', 'right': (bpr - 55) + 'px', 'top': (bpt + 25) + 'px'});
        $(pid).fadeIn(250).load(core_system_url + '/' + jrDocs_url + '/get_sections/id=' + Number(item_id) + '/order=' + order + '/profile_id=' + Number(profile_id) + '/__ajax=1');
    }
}

/**
 * Hide the Create New Section drop down
 */
function jrDocs_hide() {
    $(".new_section_box").fadeOut(100);
}
