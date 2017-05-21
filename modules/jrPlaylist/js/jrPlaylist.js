// Jamroom Playlist Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC

/**
 * show the playlist to add this item to.
 */
function jrPlaylist_select(id, mod, pn)
{
    var pid = '#playlist_' + mod + '_' + id;
    var url = '';
    if (typeof pn == "undefined" || pn == null) {
        pn = 1;
    }
    if ($(pid).is(':visible')) {
        jrPlaylist_hide();
        $(pid).fadeOut(100);
    }
    else {
        $('.overlay').hide();
        jrPlaylist_position(mod, id);
        url = core_system_url + '/' + jrPlaylist_url + '/add/' + mod + '/' + Number(id) + '/p=' + Number(pn) + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            'type': 'GET',
            'url': url,
            cache: false,
            success: function(r)
            {
                $(pid).html(r).fadeIn(200);
            }
        });
    }
}

/**
 * position the playlist on the page via javascript so it doesnt get hidden
 * by the overflow hidden on the .row class.
 */
function jrPlaylist_position(module, item_id)
{
    var bid = $('#playlist_button_' + module + '_' + item_id);
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    $('#playlist_' + module + '_' + item_id).appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
}

/**
 * remove an item from a playlist
 * @param {string} dom_id DOM ID of item to remove on success
 * @param {int} playlist_id Playlist id (will be string for non logged in users)
 * @param {string} playlist_for Module Item belongs to
 * @param {int} item_id Item ID of item being removed
 */
function jrPlaylist_remove(dom_id, playlist_id, playlist_for, item_id)
{
    var url = core_system_url + '/' + jrPlaylist_url + '/remove_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            playlist_id: playlist_id,
            playlist_for: playlist_for,
            item_id: item_id
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg)
        {
            if (_msg.success) {
                $(dom_id).slideUp(150, function()
                {
                    $(dom_id).remove();
                });
            }
            else {
                alert('error received trying to remove item: ' + _msg.error_msg);
            }
        }
    });
}

/**
 * add the selected item to the playlist
 */
function jrPlaylist_inject(playlist_id, item_id, module)
{
    $('.playlist_button').prop('disabled', true).addClass('form_button_disabled');
    var url = core_system_url + '/' + jrPlaylist_url + '/inject_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            playlist_id: playlist_id,
            item_id: item_id,
            playlist_for: module
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg)
        {
            if (_msg.success) {
                $('.playlist_button').prop('disabled', false).removeClass('form_button_disabled');
                jrPlaylist_hide();
            }
            else {
                $('.playlist_button').prop('disabled', false).removeClass('form_button_disabled');
                $('#playlist_message').addClass('playlist_error').text(_msg.success_msg);
            }
        }
    });
}

/**
 * add the selected item to the playlist
 */
function jrPlaylist_new(item_id, module)
{
    $('.playlist_button').prop('disabled', true).addClass('form_button_disabled');
    $('#playlist_indicator').show();
    setTimeout(function()
    {
        var playlist_title = $('#new_playlist_' + item_id).val();
        if (playlist_title.length > 0) {
            var url = core_system_url + '/' + jrPlaylist_url + '/add_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                data: {
                    title: playlist_title,
                    item_id: item_id,
                    playlist_for: module
                },
                cache: false,
                dataType: 'json',
                url: url,
                success: function(_msg)
                {
                    if (_msg.success) {
                        $('#playlist_message').addClass('success').text(_msg.success_msg).show();
                        setTimeout(function()
                        {
                            jrPlaylist_hide();
                            $('#playlist_indicator').hide();
                            $('.playlist_button').prop('disabled', false).removeClass('form_button_disabled');
                        }, 1200);
                    }
                    else {
                        $('#playlist_indicator').hide();
                        $('#playlist_message').addClass('error').text(_msg.success_msg).show();
                        $('.playlist_button').prop('disabled', false).removeClass('disabled');
                    }
                }
            });

        }
        else {
            $('#playlist_message').addClass('error').text('please enter a playlist name').show();
        }
    }, 1000);
}

/**
 * the X button to hide the playlist list box.
 */
function jrPlaylist_hide()
{
    $(".playlist_box").fadeOut(100);
}
