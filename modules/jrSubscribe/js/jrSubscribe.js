// Jamroom Subscriptions Module Javascript
// @copyright 2003-2017 by Talldude Networks LLC

/**
 * set_cookie
 */
function jrSubscribe_set_cookie(c)
{
    if (typeof c === "undefined" || c === null) {
        c = location.href;
    }
    return jrSetCookie('jr_sub_action', c, 1);
}

/**
 * Get a subscription price in cents
 * @param i number Plan ID
 * @param f bool format
 */
function jrSubscribe_get_sub_price(i,f)
{
    var p = $('#sub-price-' + Number(i)).val();
    if (typeof p !== "undefined") {
        p = Number(p.replace(/\D/g,''));
        if (typeof p === "number") {
            if (f !== null && f === 1) {
                return (p / 100).toFixed(2);
            }
            return p;
        }
    }
    return 0;
}

/**
 * Show modal to get variable subscription price
 * @param i number Plan ID
 * @param s string currency symbol
 */
function jrSubscribe_set_sub_price(i, s)
{
    var p = $('#sub-price-' + Number(i)).val();
    $('#sub-plan-id').val(i);
    $('#sub-currency').val(s);
    $('#sub-price-text').attr('placeholder', p);
    $('#sub-price-modal').modal();
}

/**
 * Save a variable price
 */
function jrSubscribe_save_price()
{
    var p = $('#sub-price-text').val();
    var i = $('#sub-plan-id').val();
    var c = $('#sub-currency').val();
    var url = core_system_url + '/' + jrSubscribe_url + '/check_price/__ajax=1';
    jrCore_set_csrf_cookie(url);
    $.ajax({
        type: 'POST',
        url: url,
        data: {price: p},
        cache: false,
        dataType: 'json',
        success: function(r) {
            if (typeof r.error !== "undefined") {
                if (r.error.indexOf('minimum') !== -1) {
                    $('#sub-price-text').val('1.00');
                }
                jrCore_alert(r.error);
            }
            else {
                $('#sub-price-' + Number(i)).val(c + p);
                $.modal.close();
            }
        }
    });
}