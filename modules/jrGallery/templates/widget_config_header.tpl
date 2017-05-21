{jrCore_module_url module="jrGallery" assign="murl"}
<script type="text/javascript">
    function jrGallery_widget_load_images(p, sstr, ids, si)
    {
        if (typeof sstr !== "undefined" && sstr.length > 0) {
            if (typeof si === "undefined") {
                $('#gallery_form_submit_indicator').show(300, function()
                {
                    $('#jrGallery_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p + '/sstr=' + jrE(sstr) + '/ids=' + jrE(ids) + '/__ajax=1', function()
                    {
                        $('#gallery_form_submit_indicator').hide(300);
                        $('#gallery_sstr').val('');
                    });
                });
            }
            else {
                $('#jrGallery_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p + '/sstr=' + jrE(sstr) + '/ids=' + jrE(ids) + '/__ajax=1');
            }
        }
        else {
            $('#jrGallery_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p + '/ids=' + jrE(ids) + '/__ajax=1');
        }
    }

    var list = [];

    {* add/remove the gallery id from the list. *}
    function jrGallery_include(item_id)
    {
        var i = $.inArray(item_id, list);
        if (i === -1) {
            list.push(item_id);
        }
        else {
            list.splice(i, 1);
        }
        var c = list.join(',');
        $('#gallery_list').val(c);
    }

    {* save the values of the INPUT to the existing list array *}
    function jrGallery_list_init()
    {
        var ex = $('#gallery_list').val();
        if (ex.length > 0) {
            list = ex.split(",");
        }
    }

    $(document).ready(function()
    {
        jrGallery_list_init();
        jrGallery_widget_load_images(1, false, '{$gallery_list}');
    });
</script>

{* Search Box *}
<table class="page_content">

    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrCore" id=8 default="Search"}
            <img id="gallery_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..." style="display:none">
        </td>
        <td class="element_right search_area_right">
            <div id="gallery_search_options">
                <input type="text" onkeypress="if (event &amp;&amp; event.keyCode === 13 &amp;&amp; this.value.length &gt; 0) { var s=$('#gallery_sstr').val();jrGallery_widget_load_images(1, jrE(s));return false; }" value="" class="form_text form_text_search" id="gallery_sstr" name="search_string">
                <input type="button" onclick="var s=$('#gallery_sstr').val();jrGallery_widget_load_images(1,jrE(s));return false;" class="form_button" value="{jrCore_lang module="jrCore" id=8 default="Search"}">
                <input type="button" onclick="jrGallery_widget_load_images(1);" class="form_button" value="{jrCore_lang module="jrCore" id=29 default="Reset"}">
            </div>
        </td>
    </tr>

</table>

<div id="jrGallery_holder">
    {jrCore_lang module="jrCore" id=73 default="working..." assign="alt"}
    <div class="p20">{jrCore_image module="jrEmbed" image="spinner.gif" width="24" height="24" alt=$alt}</div>
</div>
