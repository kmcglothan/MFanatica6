// Jamroom 5 FoxyCart Bundle Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Display item bundles
 * @param uid string Unique Module-Field-ItemID
 * @return bool
 */
function jrFoxyCartBundle_display_bundles(uid)
{
    $(".bundle_box").fadeOut(100);
    var bid = $('#image_' + uid);
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    var pid = '#bundle_' + uid;
    $(pid).appendTo('body').css({'position':'absolute','right':(bpr - 35) +'px','top':(bpt + 35) + 'px'});
    if ($(pid).is(":visible")) {
        $(pid).fadeOut(100);
    }
    else {
        $(pid).fadeIn(250).load(core_system_url +'/'+ jrFoxyCartBundle_url +'/display/id='+ uid);
    }
    return true;
}

/**
 * Close any open Bundle display box
 */
function jrFoxyCartBundle_close()
{
    $(".bundle_box").fadeOut(100);
}

/**
 * show the bundle to add this item to.
 */
function jrFoxyCartBundle_select(id, field, mod, page)
{
    var pid = '#bundle_' + id;
    var url = '';
    if (page === null || typeof page == "undefined") {
        if ($(pid).is(":visible")) {
            $(pid).fadeOut(100);
            jrFoxyCartBundle_close();
        }
        else {
            $('.overlay').hide();
            jrFoxyCartBundle_position(id);
            url = core_system_url +'/'+ jrFoxyCartBundle_url +'/add/'+ jrE(mod) +'/'+ Number(id) +'/field='+ jrE(field) +'/p=1/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $(pid).fadeIn(250).load(url);
        }
    }
    else {
        url = core_system_url +'/'+ jrFoxyCartBundle_url +'/add/'+ jrE(mod) +'/'+ Number(id) +'/field='+ jrE(field) +'/p='+ page +'/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $(pid).load(url);
    }
}

/**
 * position the bundle on the page via javascript so it doesnt get hidden
 * by the overflow hidden on the .row class.
 */
function jrFoxyCartBundle_position(id)
{
    var bid = $('#bundle_button_' + id);
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    $('#bundle_' + id).appendTo('body').css({'position':'absolute','right':(bpr - 35) +'px','top':(bpt + 35) + 'px'});
}

/**
 * remove an item from a bundle
 * @param {int} bid Bundle id
 * @param {string} mod Module Item belongs to
 * @param {int} id Item ID of item being removed
 */
function jrFoxyCartBundle_remove(bid, mod, id)
{
    var url = core_system_url + '/' + jrFoxyCartBundle_url + '/remove_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            bundle_id: bid,
            bundle_module: mod,
            item_id: id
        },
        cache: false,
        dataType: 'json',
        url: url,
        success: function(r) {
            if (r.type == 'success') {
                window.location.reload();
            }
            else {
                alert('error received trying to remove item: ' + r.note);
            }
        }
    });
}

/**
 * add the selected item to the bundle
 */
function jrFoxyCartBundle_inject(bid, id, field, mod)
{
    var dc  = 'form_button_disabled';
    var btn = $('.bundle_button');
    btn.prop('disabled',true).addClass(dc);
    var url = core_system_url + '/' + jrFoxyCartBundle_url + '/inject_save/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        data: {
            bundle_id: bid,
            item_id: id,
            bundle_module: mod,
            field: field
        },
        cache: false,
        dataType: 'json',
        url: url,
        success:function(r) {
            if (r.type == 'success') {
                btn.prop('disabled',false).removeClass(dc);
                jrFoxyCartBundle_close();
            }
            else {
                btn.prop('disabled',false).removeClass(dc);
                $('#bundle_message').addClass('error').text(r.note).show();
            }
        }
    });
}

/**
 * add the selected item to the bundle
 */
function jrFoxyCartBundle_new(id, field, mod)
{
    var dc = 'form_button_disabled';
    var bc = $('#bundle_close');
    var bm = $('#bundle_message');
    var bi = $('#bundle_indicator');
    var bb = $('#bundle_button');
    bc.hide();
    bm.hide();
    bi.show();
    $('.bundle_button').prop('disabled',true).addClass(dc);
    setTimeout(function () {
        var ttl = $('#new_bundle_'+ id).val();
        var prc = $('#bundle_price_'+ id).val();
        if (ttl.length > 0) {
            var url = core_system_url + '/' + jrFoxyCartBundle_url + '/add_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                data: {
                    title: ttl,
                    bundle_price: prc,
                    item_id: Number(id),
                    bundle_module: mod,
                    field: field
                },
                cache: false,
                dataType: 'json',
                url: url,
                success:function(r) {
                    if (r.type == 'success') {
                        bi.hide();
                        bm.removeClass('error').addClass('success').text(r.note).show();
                        setTimeout(function () {
                            jrFoxyCartBundle_close();
                            bc.show();
                            bb.prop('disabled',false).removeClass(dc);
                        },1200);
                    }
                    else {
                        bc.show();
                        bi.hide();
                        bm.addClass('error').text(r.note).show();
                        bb.prop('disabled',false).removeClass(dc);
                    }
                }
            });

        }
        else {
            bm.addClass('error').text('please enter a bundle name').show();
            bi.hide();
            bb.prop('disabled',false).removeClass(dc);
        }
    }, 1000);
}
