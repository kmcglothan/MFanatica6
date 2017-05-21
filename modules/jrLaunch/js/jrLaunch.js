// Jamroom Launch Module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Signup for Beta Launch
 * @return bool
 */
function jrLaunch_signup()
{
    var lsb = $('.launch_submit_button');
    var lnt = $('#launch_notice');
    lsb.attr("disabled", "disabled").addClass('lsb_disabled');
    var timeout = setTimeout(function()
    {
        var values = $('#launch_form').serializeArray();
        var lc_url = core_system_url + '/' + jrLaunch_url + '/signup_save/__ajax=1';
        jrCore_set_csrf_cookie(lc_url);
        $.ajax({
            type: 'POST',
            url: lc_url,
            data: values,
            cache: false,
            dataType: 'json',
            success: function(data)
            {
                // Check for error
                if (typeof data.error !== "undefined") {
                    $('.launch_email').addClass('launch_email_error').focus();
                    lsb.removeAttr("disabled", "disabled").removeClass('lsb_disabled');
                    lnt.text(data.error).fadeIn(250);
                }
                else {
                    $('#launch_form').hide();
                    lnt.text(data.success).addClass('launch_notice_success').fadeIn(250);
                }
            },
            error: function()
            {
                lsb.removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                lnt.text('error communicating with server - please try again').addClass('launch_notice_error').fadeIn(250);
            }
        });
        clearTimeout(timeout);
    }, 1000);
}