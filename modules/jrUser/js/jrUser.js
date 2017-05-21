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

/**
 * Show delete user modal
 * @param u number User ID
 * @param p number Profile ID
 */
function jrUser_delete_user(u, p)
{
    $('#user-delete-active-user-id').text(u);
    $('#user-delete-active-profile-id').text(p);
    $('#modal_window').modal();
}

/**
 * Delete a user
 */
function jrUser_delete_user_from_modal()
{
    var i = $('#user-delete-active-user-id').text();
    var u = core_system_url + '/' + jrUser_url + '/delete_save/id=' + Number(i);
    jrCore_set_csrf_cookie(u);
    jrCore_window_location(u);
}

/**
 * Delete a profile
 */
function jrUser_delete_profile_from_modal()
{
    var i = $('#user-delete-active-profile-id').text();
    var u = core_system_url + '/' + jrProfile_url + '/delete_save/id=' + Number(i);
    jrCore_set_csrf_cookie(u);
    jrCore_window_location(u);
}
