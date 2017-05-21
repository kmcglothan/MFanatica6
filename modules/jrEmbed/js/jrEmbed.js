/**
 * Display a tab in the Embed window
 * @param m {string} module to display tab for
 * @param p {number} page number
 * @param ss {string} Search String
 */
function jrEmbed_load_module(m, p, ss)
{
    var i = $('#embed_panel');
    var s = $('#embed_spinner');
    i.fadeOut(100, function()
    {
        $('.page_tab').removeClass('page_tab_active');
        $('#t' + m).addClass('page_tab_active');
        s.fadeIn(100, function()
        {
            var u = core_system_url + '/' + jrEmbed_url + '/load_module/m=' + jrE(m) + '/p=' + Number(p) + '/ss=' + jrE(ss) + '/__ajax=1';
            i.load(u, function()
            {
                s.fadeOut(100, function()
                {
                    i.fadeIn(100);
                    $('#jrembed_amod').text(m);
                });
            });
        });
    });
}