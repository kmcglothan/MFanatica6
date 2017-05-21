{jrCore_module_url module="jrAudio" assign="murl"}
<script type="text/javascript">
    function jrAudio_widget_pagination(p, sstr, ids)
    {
        if (typeof sstr !== "undefined" && sstr.length > 0) {
            $('#audio_form_submit_indicator').show(300, function()
            {
                $('#jrAudio_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p + '/sstr=' + jrE(sstr) + '/ids=' + jrE(ids) + '/__ajax=1', function()
                {
                    $('#audio_form_submit_indicator').hide(300);
                    $('#audio_sstr').val('');
                });
            });
        }
        else {
            $('#jrAudio_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + '/sstr=undefined/ids=' + jrE(ids) + '/__ajax=1');
        }
    }

    var playlist = [];

    {* add/remove the audio id from the playlist. *}
    function jrAudio_include(id)
    {
        var i = $.inArray(id, playlist);
        if (i == -1) {
            playlist.push(id);
        }
        else {
            playlist.splice(i, 1);
        }
        var c = playlist.join(',');
        $('#audio_playlist').val(c);
    }

    {* save the values of the INPUT to the existing playlist array *}
    function jrAudio_playlist_init()
    {
        var ex = $('#audio_playlist').val();
        if (ex.length > 0) {
            playlist = ex.split(",");
        }
    }

    $(document).ready(function()
    {
        jrAudio_playlist_init();
        jrAudio_widget_pagination(1, false, '{$audio_playlist}');
    });
</script>

{* Search Box *}
<table class="page_content">
    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrCore" id="8" default="Search"}
            <img id="audio_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..." style="display:none">
        </td>
        <td class="element_right search_area_right">
            <div id="audio_search_options">
                <input type="text" onkeypress="if (event &amp;&amp; event.keyCode == 13 &amp;&amp; this.value.length &gt; 0) { var s=$('#audio_sstr').val();jrAudio_widget_pagination(1,jrE(s));return false; }" value="" class="form_text form_text_search" id="audio_sstr" name="search_string">
                <input type="button" onclick="var s=$('#audio_sstr').val();jrAudio_widget_pagination(1, jrE(s));return false;" class="form_button" value="{jrCore_lang module="jrCore" id="8" default="Search"}">
                <input type="button" onclick="jrAudio_widget_pagination(1);" class="form_button" value="{jrCore_lang module="jrCore" id="29" default="Reset"}">
            </div>
        </td>
    </tr>
</table>

<div id="jrAudio_holder">
    {jrCore_lang module="jrCore" id="73" default="working..." assign="alt"}
    <div class="p20">{jrCore_image module="jrEmbed" image="spinner.gif" width="24" height="24" alt=$alt}</div>
</div>