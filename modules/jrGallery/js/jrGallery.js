// Jamroom Gallery Module Javascript
// @copyright 2003-2013 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Load prev/next page in gallery slider
 * @param pid int profile id
 * @param gallery string gallery name
 * @param page int page number
 * @param pagebreak int number of images per page
 */
function jrGallery_slider(pid, gallery, page, pagebreak)
{
    var url = core_system_url + '/' + jrGallery_url + '/slider_images/__ajax=1';
    $.post(url,
        {
            pid: Number(pid),
            gallery: gallery,
            page: Number(page),
            pagebreak: Number(pagebreak)
        },
        function(data)
        {
            if (data.error) {
                $('#gallery_slider').text(data.error);
            }
            else {
                $('#gallery_slider').html(data);
            }
        }
    );
}

/**
 * Set number of images wide
 * @param ct int Number of images wide
 */
function jrGallery_xup(ct)
{
    var w = Math.floor(99.9 / Number(ct));
    jrSetCookie('jr_gallery_xup_width', w, 1);
    $('.sortable > li').css('width', w + '%');
    return false;
}

/**
 * Insert an image into a page via embed
 * @param url string
 * @param title string
 */
function jrGallery_insert_image(url, title)
{
    var ed = top.tinymce.activeEditor, dom = ed.dom;
    var s = $('#imgsizg').val();
    var p = $('#imgposg').val();
    var m = $('#imgmarg').val();
    if (s === '') {
        s = 'icon';
    }
    switch (p) {
        case 'stretch':
            p = 'width:100%;';
            break;
        case 'left':
            p = 'float:left;';
            break;
        case 'right':
            p = 'float:right;';
            break;
        case '':
            p = '';
            break;
    }
    switch (m) {
        case 0:
            m = '';
            break;
        default:
            m = 'margin:' + m + 'px;';
            break;
    }
    ed.insertContent(dom.createHTML('img', {
        src: core_system_url + url + '/' + s,
        alt: title,
        title: title,
        border: 0,
        style: p + m
    }));
    var h = $(ed.getContainer()).height();
    if (h < s) {
        ed.theme.resizeTo('100%', s);
    }
    ed.windowManager.close();
}

/**
 * Toggle between "original" and "cropped" aspect ratio
 * @param a string
 */
function jrGallery_toggle_aspect(a)
{
    if (a === "" || a === 'cropped') {
        jrSetCookie('aspect', JSON.stringify('original'));
    }
    else {
        jrSetCookie('aspect', JSON.stringify('cropped'));
    }
    var p = $('#galpnum').text();
    if (typeof p === "undefined") {
        p = 1;
    }
    jrEmbed_load_module('jrGallery', p, '');
}


/**
 * ajax delete an image from the update screen of the gallery
 * @param item_id string
 */
function jrGallery_update_delete(item_id)
{

    var url = core_system_url + '/' + jrGallery_url + '/delete_image_ajax/id=' + item_id + '/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $('#del_spin_' + item_id).show(300, function()
    {
        $('#del_btn_' + item_id).fadeOut('fast');
        $.ajax({
            type: 'POST',
            url: url,
            cache: false,
            dataType: 'json',
            success: function(data)
            {
                if (data.OK) {
                    // remove the image
                    $('#gi_' + item_id).fadeOut('slow').remove();
                }
                else {
                    jrCore_alert(data.error);
                }
                return true;
            }
        });
    });
}


/**
 * Save a title to a single image from the gallery update form
 */
function jrGallery_save_title(id)
{
    var i = $('#gallery_update_id').val();
    var l = $('#gallery_update_title').val();
    var s = $('#gallery_title_save');
    s.attr('disabled', 'disabled').addClass('form_button_disabled');
    setTimeout(function()
    {
        var u = core_system_url + '/' + jrGallery_url + '/image_title_save/__ajax=1';
        jrCore_set_csrf_cookie(u);
        $.ajax({
            url: u,
            type: 'POST',
            cache: false,
            data: {'id': Number(i), 'gallery_image_title': l},
            dataType: 'json',
            success: function(data)
            {
                if (data.OK == 1) {
                    $.modal.close();
                    $('#gallery_image_title_' + i).text(data.gallery_image_title);
                }
                else {
                    $.modal.close();
                    jrCore_alert(data.error);
                }
            },
            error: function()
            {
                jrCore_alert('error communicating with server - please try again');
            }
        });
    }, 500);
}
