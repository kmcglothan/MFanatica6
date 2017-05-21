// Jamroom jrRating Javascript
// @copyright 2003-2011 by Talldude Networks LLC
// @author Paul Asher - paul@jamroom.net

/**
 * Send a Rating request to the jrRating rate_item view
 * @param html_id string Unique ID for this rating
 * @param rating int 1 = 5 rating number
 * @param module_url module URL for item being rated
 * @param item_id int Item ID
 * @param index int rating index
 * @param target string Target for result
 * @return null
 */
function jrRating_rate_item(html_id, rating, module_url, item_id, index, target)
{
    var i = $(html_id);
    var w = i.parent().width();
    i.parent().animate({width:0},300,function() {
        var url = core_system_url + '/' + module_url + '/rate/' + item_id + '/' + rating + '/' + index + '/__ajax=1';
        jrCore_set_csrf_cookie(url);
        $.ajax({
            type:'POST',
            url: url,
            data:{},
            dataType:'json',
            success:function (data) {
                if (typeof target !== "undefined" && target == 'alert') {
                    if (data.OK) {
                        alert('Success - Average: ' + data.rating_average + ' (' + data.rating_count + ' Votes)');
                    }
                    else if (data.error) {
                        alert(data.error);
                    }
                }
                // Default to in place update
                else {
                    if (data.OK) {
                        i.css('width',Number(data.rating_average) * 20 +'%').css('background-position','left center').css('z-index','6');
                    }
                    i.parent().animate({ width: w },100);
                }
            },
            error:function () {
                alert('an error was encountered saving the rating - please try again');
            }
        });
    });
}

