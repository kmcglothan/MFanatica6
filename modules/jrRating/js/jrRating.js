// Jamroom jrRating Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Paul Asher - paul@jamroom.net

/**
 * Send a Rating request to the jrRating rate_item view
 * @param id string Unique ID for this rating
 * @param r int 1 = 5 rating number
 * @param mu module URL for item being rated
 * @param ii int Item ID
 * @param x int rating index
 * @param t string Target for result
 * @return null
 */
function jrRating_rate_item(id, r, mu, ii, x, t)
{
    var i = $(id);
    var w = i.parent().width();
    i.parent().animate({width: 0}, 300, function() {
        var url = core_system_url + '/' + mu + '/rate/' + ii + '/' + r + '/' + x + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type: 'POST',
            url: url,
            data: {},
            dataType: 'json',
            success: function(r) {
                if (typeof t !== "undefined" && t === 'alert') {
                    if (r.OK) {
                        jrCore_alert('Success - Average: ' + r.rating_average + ' (' + r.rating_count + ' Votes)');
                    }
                    else if (r.error) {
                        jrCore_alert(r.error);
                    }
                }
                else {
                    if (typeof r.error !== "undefined") {
                        if (r.error == 'login') {
                            jrCore_window_location(core_system_url + '/' + jrUser_url + '/login/r=1');
                        }
                        else {
                            i.parent().animate({width: w}, 100);
                            jrCore_alert(r.error);
                        }
                    }
                    else {
                        // in place update
                        if (r.OK) {
                            i.css('width', Number(r.rating_average) * 20 + '%').css('background-position', 'left center').css('z-index', '6');
                        }
                        i.parent().animate({width: w}, 100);
                    }
                }
            },
            error: function() {
                jrCore_alert('an error was encountered saving the rating - please try again');
            }
        });
    });
}

