/**
 * jrSiteBuilder PUBLIC Javascript functions
 * @copyright 2015 Talldude Networks, LLC.
 */

/**
 * Load new widget content into a container ( tabbed container )
 * @param p {int} page_id
 * @param l {int} page location
 * @param i {int} widget_id
 */
function jrSiteBuilder_load_tab(p, l, i)
{
    var h = $('#t' + i);
    $('#c' + l + ' li').removeClass('page_tab_active');
    h.addClass('page_tab_active');
    $('#l' + p + '-location-' + l + ' .sb-content-active').removeClass('sb-content-active').fadeOut(100, function()
    {
        $('#w' + i).fadeIn(100).addClass('sb-content-active');
    });
    location.hash = h.data('widget_hash');
}
