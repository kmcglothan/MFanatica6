// Jamroom FoxyCart Bundle Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Display item bundles
 */
function jrBundle_display_bundles(o, id)
{
    var pid = $('.bundle_drop_down');
    if (pid.is(":visible")) {
        pid.fadeOut(100);
    }
    else {
        var bid = $(o);
        var bpr = $(window).width() - bid.offset().left;
        var bpt = bid.offset().top;
        pid.css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
        $.ajax({
            type: 'GET',
            cache: false,
            dataType: 'html',
            url: core_system_url + '/' + jrBundle_url + '/display/id=' + id,
            success: function(r)
            {
                if (typeof r !== 'undefined' && r !== null && r.indexOf('ERROR') === 0) {
                    if (r.indexOf('not found') !== -1) {
                        window.location.reload();
                    }
                    else {
                        jrCore_alert(r);
                    }
                }
                else {
                    pid.html(r).fadeIn(200);
                }
            }
        });
    }
    return true;
}

/**
 * Close any open Bundle display box
 */
function jrBundle_close()
{
    $('.bundle_box').fadeOut(100);
}

/**
 * show the bundle to add this item to.
 */
function jrBundle_select(id, field, mod, page)
{
    var pid = '#bundle_' + id;
    var url = '';
    if (page === null || typeof page === "undefined") {
        if ($(pid).is(":visible")) {
            $(pid).fadeOut(100);
            jrBundle_close();
        }
        else {
            $('.overlay').hide();
            jrBundle_position(id);
            url = core_system_url + '/' + jrBundle_url + '/select/' + jrE(mod) + '/' + Number(id) + '/field=' + jrE(field) + '/p=1/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $(pid).fadeIn(250).load(url);
        }
    }
    else {
        url = core_system_url + '/' + jrBundle_url + '/select/' + jrE(mod) + '/' + Number(id) + '/field=' + jrE(field) + '/p=' + page + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $(pid).load(url);
    }
}

/**
 * position the bundle on the page
 */
function jrBundle_position(id)
{
    var bid = $('#bundle_button_' + id);
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    $('#bundle_' + id).appendTo('body').css({'position': 'absolute', 'right': (bpr - 35) + 'px', 'top': (bpt + 35) + 'px'});
}

/**
 * remove an item from a bundle
 * @param {int} bid Bundle id
 * @param {string} mod Module Item belongs to
 * @param {int} id Item ID of item being removed
 */
function jrBundle_remove(bid, mod, id)
{
    var url = core_system_url + '/' + jrBundle_url + '/remove_save/__ajax=1';
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
        success: function(r)
        {
            if (r.type === 'success') {
                window.location.reload();
            }
            else {
                jrCore_alert('error received trying to remove item: ' + r.note);
            }
        }
    });
}

/**
 * add the selected item to the bundle
 */
function jrBundle_inject(bid, id, field, mod)
{
    var dc = 'form_button_disabled';
    var btn = $('.bundle_button');
    btn.prop('disabled', true).addClass(dc);
    var url = core_system_url + '/' + jrBundle_url + '/inject_save/__ajax=1';
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
        success: function(r)
        {
            if (r.type === 'success') {
                btn.prop('disabled', false).removeClass(dc);
                jrBundle_close();
            }
            else {
                btn.prop('disabled', false).removeClass(dc);
                $('#bundle_message').addClass('error').text(r.note).show();
            }
        }
    });
}

/**
 * add the selected item to the bundle
 */
function jrBundle_new(id, field, mod)
{
    var dc = 'form_button_disabled';
    var bc = $('#bundle_close');
    var bm = $('#bundle_message');
    var bi = $('#bundle_indicator');
    var bb = $('#bundle_button');
    bc.hide();
    bm.hide();
    bi.show();
    $('.bundle_button').attr("disabled", "disabled").addClass(dc);
    $('.field-hilight').removeClass('field-hilight');
    setTimeout(function()
    {
        var ttl = $('#new_bundle_' + id);
        var prc = $('#bundle_price_' + id);
        if (ttl.val().length > 0 && prc.val().length > 0) {
            var url = core_system_url + '/' + jrBundle_url + '/add_save/__ajax=1';
            jrCore_set_csrf_cookie(url);
            $.ajax({
                type: 'POST',
                data: {
                    title: ttl.val(),
                    bundle_price: prc.val(),
                    item_id: Number(id),
                    bundle_module: mod,
                    field: field
                },
                cache: false,
                dataType: 'json',
                url: url,
                success: function(r)
                {
                    if (r.type === 'success') {
                        bi.hide();
                        bm.removeClass('error').addClass('success').text(r.note).show();
                        setTimeout(function()
                        {
                            jrBundle_close();
                            bc.show();
                            bb.removeAttr('disabled').removeClass(dc);
                        }, 1000);
                    }
                    else {
                        bc.show();
                        bi.hide();
                        bm.addClass('error').text(r.note).show();
                        bb.removeAttr('disabled').removeClass(dc);
                    }
                }
            });

        }
        else {
            if (ttl.val().length === 0) {
                ttl.addClass('field-hilight');
            }
            if (prc.val().length === 0) {
                prc.addClass('field-hilight');
            }
            bi.hide();
            $('.bundle_button').removeAttr('disabled').removeClass(dc);
        }
    }, 750);
}
