{jrCore_module_url module="jrSoundCloud" assign="murl"}
<script type="text/javascript">
    function jrSoundCloud_widget_pagination(p, sstr, sel)
    {
        if (typeof sstr !== "undefined" && sstr.length > 0) {
            $('#soundcloud_form_submit_indicator').show(300, function()
            {
                $('#jrSoundCloud_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p + '/sstr=' + jrE(sstr) + '/sel=' + sel + '/__ajax=1', function()
                {
                    $('#soundcloud_form_submit_indicator').hide(300);
                    $('#soundcloud_sstr').val('');
                });
            });
        }
        else {
            $('#jrSoundCloud_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + '/sstr=undefined/sel=' + sel + '/__ajax=1');
        }
    }

    $(document).ready(function()
    {
        jrSoundCloud_widget_pagination(1, false, '{$widget_data.soundcloud_id}');
    });
</script>

{* Search Box *}
<table class="page_content">
    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrCore" id="8" default="Search"}
            <img id="soundcloud_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..." style="display:none">
        </td>
        <td class="element_right search_area_right">
            <div id="soundcloud_search_options">
                <input type="text" onkeypress="if (event &amp;&amp; event.keyCode == 13 &amp;&amp; this.value.length &gt; 0) { var s=$('#soundcloud_sstr').val();jrSoundCloud_widget_pagination(1,jrE(s));return false; }" value="" class="form_text form_text_search" id="soundcloud_sstr" name="search_string">
                <input type="button" onclick="var s=$('#soundcloud_sstr').val();jrSoundCloud_widget_pagination(1, jrE(s));return false;" class="form_button" value="{jrCore_lang module="jrCore" id="8" default="Search"}">
                <input type="button" onclick="jrSoundCloud_widget_pagination(1);" class="form_button" value="{jrCore_lang module="jrCore" id="29" default="Reset"}">
            </div>
        </td>
    </tr>
</table>

<div id="jrSoundCloud_holder">
    {jrCore_lang module="jrCore" id="73" default="working..." assign="alt"}
    <div class="p20">{jrCore_image module="jrEmbed" image="spinner.gif" width="24" height="24" alt=$alt}</div>
</div>