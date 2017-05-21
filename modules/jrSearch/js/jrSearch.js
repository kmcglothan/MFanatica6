/**
 * jrSearch Javascript functions
 * @copyright 2012 Talldude Networks, LLC.
 */

/**
 * Search for results within a specific module
 */
function jrSearch_module_index(url, fields)
{
    var ss = $('#search_module').val();
    if (ss.length > 0) {
        $('#form_submit_indicator').show(300, function()
        {
            window.location = core_system_url + '/' + url + '/ss=' + jrE(ss);
        });
    }
    return false;
}

/**
 * Display a modal search form
 */
function jrSearch_modal_form()
{
    $('#searchform').modal({

        onOpen: function(d)
        {
            d.overlay.fadeIn(75, function()
            {
                d.container.slideDown(0, function()
                {
                    d.data.fadeIn(300, function()
                    {
                        $('#searchform .form_text').focus();
                    });
                });
            });
        },
        onClose: function(d)
        {
            d.data.fadeOut('fast', function()
            {
                d.container.hide('fast', function()
                {
                    d.overlay.fadeOut('fast', function()
                    {
                        $.modal.close();
                    });
                });
            });
        },
        overlayClose: true
    });
}

/**
 * Re-run search on a result page
 */
function jrSearch_refine_results()
{
    $('#form_submit_indicator').show(300, function()
    {
        setTimeout(function()
        {
            $('#sr').submit();
        }, 300);
    });
}