// Jamroom Combined Audio Javascript
// @copyright 2003-2015 by Talldude Networks LLC

/**
 * Display Create Audio options
 * @return bool
 */
function jrCombinedAudio_create_audio()
{
    var bid = $('#create_audio_button');
    var bpr = $(window).width() - bid.offset().left;
    var bpt = bid.offset().top;
    var pid = $('#create_audio_dropdown');
    pid.css({'position':'absolute','right':(bpr - 35) +'px','top':(bpt + 20) + 'px'});
    if (pid.is(":visible")) {
        pid.fadeOut(100);
    }
    else {
        pid.load(core_system_url +'/'+ jrCombinedAudio_url +'/create_audio/__ajax=1', function() {
            pid.fadeIn(250);
        });
    }
    return true;
}