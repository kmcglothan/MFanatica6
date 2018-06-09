// Jamroom PhotoAlbum Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC

/**
 * show the photo album to add this item to.
 */
function jrPhotoAlbum_select(id, mod, page)
{
    var pid = $('#photoalbum_' + mod + '_' + id);
    var url = '';
    if (page === null || typeof page === "undefined") {
        if (pid.is(":visible")) {
            jrPhotoAlbum_hide();
            pid.fadeOut(100);
        }
        else {
            $('.overlay').hide();
            jrPhotoAlbum_position(mod, id);
            url = core_system_url + '/' + jrPhotoAlbum_url + '/add/' + mod + '/' + id + '/p=1/__ajax=1';
            jrCore_set_csrf_cookie(url);
            pid.fadeIn(250).load(url, function()
            {
                $('#new_photoalbum_form').find('input.form_text').focus();
            });
        }
    }
    else {
        url = core_system_url + '/' + jrPhotoAlbum_url + '/add/' + mod + '/' + id + '/p=' + Number(page) + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        pid.load(url);
    }
}

/**
 * position the photo album on the page via javascript so it doesnt get hidden
 * by the overflow hidden on the .row class.
 */
function jrPhotoAlbum_position(mod, id)
{
    var bid = $('#photoalbum_button_' + mod + '_' + id);
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    $('#photoalbum_' + mod + '_' + id).appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
}

/**
 * remove an item from a photo album
 * @param {string} dom_id DOM ID of item to remove on success
 * @param {int} id PhotoAlbum id (will be string for non logged in users)
 * @param {string} mod Module Item belongs to
 * @param {int} item_id Item ID of item being removed
 */
function jrPhotoAlbum_remove(dom_id, id, mod, item_id)
{
    var url = core_system_url + '/' + jrPhotoAlbum_url + '/remove_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            photoalbum_id: id,
            photoalbum_for: mod,
            item_id: item_id
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg)
        {
            if (_msg.success) {
                $('#' + dom_id).slideUp(150, function()
                {
                    $('#' + dom_id).remove();
                });
            }
            else {
                jrCore_alert('error received trying to remove item: ' + _msg.error_msg);
            }
        }
    });
}

/**
 * add the selected item to the photo album
 */
function jrPhotoAlbum_inject(id, item_id, mod)
{
    $('.photoalbum_button').prop('disabled', true).addClass('form_button_disabled');
    var url = core_system_url + '/' + jrPhotoAlbum_url + '/inject_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            photoalbum_id: id,
            item_id: item_id,
            photoalbum_for: mod
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(_msg)
        {
            if (_msg.success) {
                $('.photoalbum_button').prop('disabled', false).removeClass('form_button_disabled');
                jrPhotoAlbum_hide();
            }
            else {
                $('.photoalbum_button').prop('disabled', false).removeClass('form_button_disabled');
                $('#photoalbum_message').addClass('photoalbum_error').text(_msg.success_msg);
            }
        }
    });
}

/**
 * Create a new Photo album
 */
function jrPhotoAlbum_new(item_id, mod)
{
    $('.photoalbum_button').prop('disabled', true).addClass('form_button_disabled');
    var i = $('#photoalbum_indicator');
    i.show();
    setTimeout(function()
    {
        var photoalbum_title = $('#new_photoalbum_' + item_id).val();
        if (photoalbum_title.length > 0) {
            var url = core_system_url + '/' + jrPhotoAlbum_url + '/add_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                data: {
                    title: photoalbum_title,
                    item_id: item_id,
                    photoalbum_for: mod
                },
                cache: false,
                dataType: 'json',
                url: url,
                success: function(_msg)
                {
                    i.hide();
                    if (_msg.success) {
                        $('#photoalbum_message').removeClass('error').addClass('success').text(_msg.success_msg).show();
                        setTimeout(function()
                        {
                            jrPhotoAlbum_hide();
                            $('.photoalbum_button').prop('disabled', false).removeClass('form_button_disabled');
                        }, 1500);
                    }
                    else {
                        $('#photoalbum_message').addClass('error').text(_msg.success_msg).show();
                        $('.photoalbum_button').prop('disabled', false).removeClass('disabled');
                    }
                }
            });
        }
        else {
            $('#photoalbum_message').addClass('error').text('please enter a photo album name').show();
        }
    }, 500);
}

/**
 * the X button to hide the photoalbum list box.
 */
function jrPhotoAlbum_hide()
{
    $(".photoalbum_box").fadeOut(100);
}

/**
 * Set number of images wide
 * @param ct int Number of images wide
 */
function jrPhotoAlbum_xup(ct)
{
    var w = Math.floor(99.9 / Number(ct));
    jrSetCookie('jr_photoalbum_xup_width', w, 1);
    $('ul.sortable > li').css('width', w + '%');
    return false;
}
