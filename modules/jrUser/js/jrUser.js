// Jamroom User Module Javascript
// @copyright 2003-2016 by Talldude Networks LLC

/**
 * Show a notification option
 * @param v string ID to show
 */
function jrUser_notification_option(v)
{
    $('.no-act').removeClass('no-act').fadeOut(250, function() {
        $('#' + v).fadeIn(150).addClass('no-act'); 
    });
}
