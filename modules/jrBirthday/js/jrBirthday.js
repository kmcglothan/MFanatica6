/**
 * Submit new birthday wish
 */
function jrBirthday_submit()
{
    if ($('#birthday_update').val().length < 1) {
        return false;
    }
    $('#birthday_submit').attr('disabled', 'disabled').addClass('form_button_disabled');
    var a = $('#birthday_share_indicator');
    a.show(300, function()
    {
        setTimeout(function()
        {
            var f = $('#birthday_share_form');
            $.post(f.attr('action'), f.serializeArray(), function(r)
            {
                if (typeof r.error !== "undefined") {
                    a.hide(300, function()
                    {
                        $('#birthday_submit').removeAttr('disabled').removeClass('form_button_disabled');
                        alert(r.error);
                    });
                }
                else {
                    window.location.reload();
                }
            }, 'json');
        }, 200);
    });
}