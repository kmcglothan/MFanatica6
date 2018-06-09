// Jamroom Combined Video Javascript
// @copyright 2003-2015 by Talldude Networks LLC

/**
 * Display Create Video options
 * @return bool
 */
function jrCombinedVideo_create_video()
{
    var bid = $('#create_video_button');
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    var pid = $('#create_video_dropdown');
    pid.css({'position':'absolute','right':(bpr - 35) +'px','top':(bpt + 20) + 'px'});
    if (pid.is(":visible")) {
        pid.fadeOut(100);
    }
    else {
        pid.load(core_system_url +'/'+ jrCombinedVideo_url +'/create_video/__ajax=1', function() {
            pid.fadeIn(250);
        });
    }
    return true;
}