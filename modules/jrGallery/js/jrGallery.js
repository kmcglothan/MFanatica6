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