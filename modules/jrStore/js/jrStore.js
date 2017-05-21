// Jamroom 5 Comment Module Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Submits a new Comment
 * @param {string} form_id Form ID
 * @param {string} target DOM element to reload comments into
 * @return bool
 */
function jrStoreComment(form_id, target) {
    $(form_id + ' #comment_submit').attr("disabled", "disabled").addClass('form_button_disabled');
    $('#form_submit_indicator').show(300, function () {
        $('#comment_notice').hide();
        var timeout = setTimeout(function () {
            var values = $(form_id).serializeArray();
            var st_url = core_system_url + '/' + jrStore_url + '/comment_save/__ajax=1';
            jrCore_set_csrf_cookie(st_url);
            $.ajax({
                type: 'POST',
                url: st_url,
                data: values,
                cache: false,
                dataType: 'json',
                success: function (data) {
                    // Check for error
                    if (typeof data.error !== "undefined") {
                        $('#comment_notice').text(data.error);
                        $('#form_submit_indicator').hide(300, function () {
                            $(form_id + ' #comment_submit').removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                            $('#comment_notice').fadeIn(250);
                        });
                    }
                    else {
                        // Reload comments into target
                        $(form_id + ' textarea').val('');
                        $(form_id + ' #comment_submit').removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                        $('#form_submit_indicator').hide(300, function () {
                            var txn_id = $(form_id + ' #comment_txn_id').val();
                            var seller_profile_id = $(form_id + ' #comment_seller_profile_id').val();
                            $(target).load(core_system_url + '/' + jrStore_url + '/view_comments/txn_id=' + txn_id + '/seller_profile_id=' + seller_profile_id + '/__ajax=1', function () {
                                $('#comment_success').fadeIn(250, function () {
                                    $('#comment_section').scrollintoview();
                                    $('#comment_success').delay(4000).fadeOut(500);
                                });
                            });
                        });
                    }
                },
                error: function (x, t, e) {
                    $('#form_submit_indicator').hide(300, function () {
                        $(form_id + ' #comment_submit').removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                        $('#comment_notice').text('Error communicating with server - please try again').show();
                    });
                }
            });
            clearTimeout(timeout);
        }, 1000);
    });
}

/**
 * change the status of the order
 */
function jrStoreStatus(txn_id, seller_profile_id) {
    $.post(core_system_url + '/' + jrStore_url + "/status_update/__ajax=1", {
        status: $('#status' + txn_id).val(),
        txn_id: txn_id,
        seller_profile_id: seller_profile_id
    }, function () {
        $('#status_success').fadeIn(250, function () {
            $(this).scrollintoview();
            $(this).delay(4000).fadeOut(500);
        });
    });

}

