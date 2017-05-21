/**
 * construct the default skin dropdown list from checkbox options in QUOTA CONFIG tab
 */
function jrProfileTweaks_default_skin_options()
{
    var sel = $('#default_skin');
    var hide = true;
    var selected = sel.val();

    sel.empty();

    $('[id^=allow_skin_]:checked').each(function()
    {
        hide = false;
        sel.append($("<option>").attr('value', $(this).data('key')).text($(this).next('.form_option_list_text').text()));

        if ($(this).data('key') == selected) {
            sel.val(selected);
        }
    });

    if (hide) {
        $('#ff-row-default_skin').hide();
    }
    else {
        $('#ff-row-default_skin').show();
    }
}
