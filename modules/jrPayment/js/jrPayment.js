// Jamroom Payments Module Javascript
// @copyright 2003-2016 by Talldude Networks LLC

/**
 * View the cart
 */
function jrPayment_view_cart()
{
    var url = core_system_url + '/' + jrPayment_url + '/cart/__ajax=1';
    if ($('#cart-modal').length === 0) {
        $('body').append('<div id="cart-modal"></div>');
    }
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'html',
        success: function(r)
        {
            $('#cart-modal').html(r).modal();
        }
    });
}

/**
 * Close the shopping cart
 */
function jrPayment_close_cart()
{
    $.modal.close();
    $('#cart-modal').hide();
}

/**
 * Add an item to the cart
 * @param m string Module
 * @param i int Item ID
 * @param f string Field
 */
function jrPayment_add_to_cart(m, i, f)
{
    var url = core_system_url + '/' + jrPayment_url + '/add_item_to_cart/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {cart_module: m, cart_item_id: i, cart_field: f},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrCore_alert(r.error);
            }
            else {
                $('#payment-view-cart-button').find('span').text(r.item_count).show();
                jrPayment_view_cart();
            }
        }
    });
}

/**
 * Reset the cart
 */
function jrPayment_reset_cart()
{
    var url = core_system_url + '/' + jrPayment_url + '/cart_reset/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error === "undefined") {
                $('#payment-view-cart-button').find('span').hide();
                $.modal.close();
                $('#cart-holder').hide();
            }
            else {
                jrCore_alert(r.error);
            }
        }
    });
}

/**
 * Remove an item from the cart
 */
function jrPayment_remove_item(id)
{
    var url = core_system_url + '/' + jrPayment_url + '/remove_item_from_cart/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {id: id},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            if (typeof r.error !== "undefined") {
                jrCore_alert(r.error);
            }
            else {
                // If this was the LAST item in the cart, close cart
                if ($('.cart-item-row').length === 1) {
                    jrPayment_close_cart();
                    $('#payment-view-cart-button').find('span').hide();
                }
                else {
                    var s = $('#payment-view-cart-button').find('span');
                    s.text(s.text() - 1);
                    jrPayment_refresh_cart();
                }
            }
        }
    });
    return false;
}

/**
 * Refresh the cart HTML
 */
function jrPayment_refresh_cart()
{
    // Get fresh cart
    var url = core_system_url + '/' + jrPayment_url + '/cart/__ajax=1';
    $.ajax({
        type: 'GET',
        url: url,
        cache: false,
        dataType: 'html',
        success: function(r)
        {
            $('#cart-modal').html(r);
        }
    });
}

/**
 * Redirect to login for checkout
 * @returns {boolean}
 */
function jrPayment_checkout_login()
{
    var url = core_system_url + '/' + jrPayment_url + '/checkout_login/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {url: window.location.href},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            jrCore_window_location(r.url);
        }
    });
    return false;

}

/**
 * Update quantity for an item
 * @param {number} t
 * @returns {boolean}
 */
function jrPayment_update_quantity(t)
{
    var q = $('#q' + t);
    var i = q.data('eid');
    var n = q.val();
    $(t).find('input').attr('disabled', 'disabled').addClass('form_button_disabled');
    var url = core_system_url + '/' + jrPayment_url + '/update_quantity_save/_ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {id: Number(i), qty: Number(n)},
        cache: false,
        dataType: 'json',
        success: function(r)
        {
            $(r).find('input').removeAttr('disabled').removeClass('form_button_disabled');
            if (typeof r.error !== "undefined") {
                jrCore_alert(r.error);
            }
            else {
                if (typeof r.adjusted !== "undefined") {
                    jrCore_confirm(r.title, r.text, function()
                    {
                        jrPayment_refresh_cart();
                    });
                }
                else {
                    jrPayment_refresh_cart();
                }
            }
        }
    });
    return false;

}
