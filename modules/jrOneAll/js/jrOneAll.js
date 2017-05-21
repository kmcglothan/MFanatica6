// Jamroom OneAll Module Javascript
// @copyright 2003-2015 by Talldude Networks LLC
// @author Brian Johnson - brian@jamroom.net

/**
 * Set Quota ID cookie
 * @returns {boolean}
 */
function jrOneAll_set_quota_id()
{
    var q = $('#quota_id').val();
    if (typeof q !== "undefined") {
        jrSetCookie('signup_quota_id', Number(q), 2);
    }
    return true;
}