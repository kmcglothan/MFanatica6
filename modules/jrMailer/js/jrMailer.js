// Jamroom Mail Core Javascript
// @copyright 2003-2016 by Talldude Networks LLC

/**
 * Get a User report
 * @param uid int User ID
 * @param cid int Campaign ID
 */
function jrMailer_user_report(uid, cid)
{
    var b = $('#top-users-box');
    $('#top-users-holder').html(b.html());
    var url = core_system_url + '/' + jrMailer_url + '/user_report/' + Number(uid) + '/' + Number(cid) + '/__ajax=1';
    b.load(url);
}

/**
 * Load top users box
 */
function jrMailer_top_users()
{
    $('#top-users-box').html($('#top-users-holder').html());
}

/**
 * Resize the campaign viewer
 */
function jrMailer_cp_resize()
{
    var s = $('#cp-display-area');
    var h = ($('body').outerHeight() - s.offset().top - $('#footer').outerHeight() - $('.form_submit_section').outerHeight());
    s.height(h);
}

/**
 * Init campaign view
 */
function jrMailer_cp_init()
{
    jrMailer_cp_resize();
    window.onresize = function()
    {
        jrMailer_cp_resize();
    }
}
