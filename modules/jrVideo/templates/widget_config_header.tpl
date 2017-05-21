{jrCore_module_url module="jrVideo" assign="murl"}
<script type="text/javascript">
    function jrVideo_widget_pagination(p, profile_url, album_url, sstr, ids)
    {
        if (typeof sstr !== "undefined" && sstr.length > 0) {
            $('#video_form_submit_indicator').show(300, function()
            {
                $('#jrVideo_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p + '/profile_url=' + jrE(profile_url) + '/album_url=' + jrE(album_url) + '/sstr=' + jrE(sstr) + '/ids=' + jrE(ids) + '/__ajax=1', function()
                {
                    $('#video_form_submit_indicator').hide(300);
                    $('#video_sstr').val('');
                });
            });
        }
        else {
            $('#jrVideo_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p + '/profile_url=' + jrE(profile_url) + '/album_url=' + jrE(album_url) + '/ids=' + jrE(ids) + '/__ajax=1');
        }
    }

    var playlist = [];

    {* add/remove the video id from the playlist. *}
    function jrVideo_include(item_id)
    {
        var i = $.inArray(item_id, playlist);
        if (i == -1) {
            playlist.push(item_id);
        }
        else {
            playlist.splice(i, 1);
        }
        var c = playlist.join(',');
        $('#video_playlist').val(c);
    }

    {* save the values of the INPUT to the existing playlist array *}
    function jrVideo_playlist_init()
    {
        var ex = $('#video_playlist').val();
        if (ex.length > 0) {
            playlist = ex.split(",");
        }
    }

    $(document).ready(function()
    {
        jrVideo_playlist_init();
        jrVideo_widget_pagination(1, false, false, false, '{$video_playlist}');
    });
</script>

{* Search Box *}
<table class="page_content">
    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrCore" id="8" default="Search"}
            <img id="video_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..." style="display:none">
        </td>
        <td class="element_right search_area_right">
            <div id="video_search_options">
                <input type="text" onkeypress="if (event &amp;&amp; event.keyCode == 13 &amp;&amp; this.value.length &gt; 0) { var s=$('#video_sstr').val();jrVideo_widget_pagination(1,false, false, jrE(s));return false; }" value="" class="form_text form_text_search" id="video_sstr" name="search_string">
                <input type="button" onclick="var s=$('#video_sstr').val();jrVideo_widget_pagination(1,false, false, jrE(s));return false;" class="form_button" value="{jrCore_lang module="jrCore" id="8" default="Search"}">
                <input type="button" onclick="jrVideo_widget_pagination(1);" class="form_button" value="{jrCore_lang module="jrCore" id="29" default="Reset"}">
            </div>
        </td>
    </tr>
</table>

<div id="jrVideo_holder">
    {jrCore_lang module="jrCore" id="73" default="working..." assign="alt"}
    <div class="p20">{jrCore_image module="jrEmbed" image="spinner.gif" width="24" height="24" alt=$alt}</div>
</div>