/**
 * Cancel this subscription
 * @param sub_token
 */
function jrFoxyCart_Subscription_Cancel(sub_token)
{
    //send the cancellation via ajax
    //sub_token is needed to cancel the transaction.  Should only be able to be cancelled by the owner or the admin
    if (confirm('Are you sure you want to Cancel this Subscription?')) {
        var url = core_system_url + '/' + jrFoxyCart_url + '/ajax?mode=subscription_cancel&sub_token=' + sub_token;
        jrCore_set_csrf_cookie(url);
        $.getJSON(url, function (data) {
            $('#row' + sub_token).addClass('strikethrough');
            $('#buttons' + sub_token).remove();
            jrFoxyCart_Current_Subscription_Info();
        });
    }
}

/**
 * display the users current subscription info
 */
function jrFoxyCart_Current_Subscription_Info()
{
    $.getJSON(core_system_url + '/' + jrFoxyCart_url + '/ajax?mode=subscription_info', function (data) {
        $('#quota_id').text(data.quota_id);
        $('#quota_name').text(data.quota_name);
        $('#sub_len').text(data.sub_len);
        $('#quota_price').text(data.quota_price);
    });
}

/**
 * change the quota if the user has multiple active subscriptions.
 * @param sub_token
 */
function jrFoxyCart_Change_Quota(sub_token)
{
    var url = core_system_url + '/' + jrFoxyCart_url + '/ajax?mode=change_quota&sub_token=' + sub_token;
    jrCore_set_csrf_cookie(url);
    $.getJSON(url, function (data) {
        jrFoxyCart_Current_Subscription_Info();
    });
}
