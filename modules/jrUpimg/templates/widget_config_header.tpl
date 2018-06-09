{jrCore_module_url module="jrUpimg" assign="murl"}
<script type="text/javascript">
    function jrUpimg_widget_load_images(p, sstr, ids) {
        if (typeof sstr !== "undefined" && sstr.length > 0) {
            $('#upimg_form_submit_indicator').show(300, function()
            {
                $('#jrUpimg_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p  + '/sstr=' + jrE(sstr) + '/ids=' + jrE(ids) + '/__ajax=1', function()
                {
                    $('#upimg_form_submit_indicator').hide(300);
                    $('#upimg_sstr').val('');
                });
            });
        }
        else {
            $('#jrUpimg_holder').load(core_system_url + '/{$murl}/widget_config_body/p=' + p + '/ids=' + jrE(ids) + '/__ajax=1');
        }
    }

    var list = [];

    {* add/remove the upimg id from the list. *}
    function jrUpimg_include(item_id)
    {
        var i = $.inArray(item_id, list);
        if (i == -1) {
            list.push(item_id);
        }
        else {
            list.splice(i, 1);
        }
        var c = list.join(',');
        $('#upimg_list').val(c);
    }

    {* save the values of the INPUT to the existing list array *}
    function jrUpimg_list_init()
    {
        var ex = $('#upimg_list').val();
        if (ex.length > 0) {
            list = ex.split(",");
        }
    }

    $(document).ready(function()
    {
        jrUpimg_list_init();
        jrUpimg_widget_load_images(1, false, '{$upimg_list}');
    });
</script>

{* Search Box *}
<table class="page_content">

    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrCore" id="8" default="Search"}
            <img id="upimg_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..." style="display:none">
        </td>
        <td class="element_right search_area_right">
            <div id="upimg_search_options">
                <input type="text" onkeypress="if (event &amp;&amp; event.keyCode == 13 &amp;&amp; this.value.length &gt; 0) { var s=$('#upimg_sstr').val();jrUpimg_widget_load_images(1, jrE(s));return false; }" value="" class="form_text form_text_search" id="upimg_sstr" name="search_string">
                <input type="button" onclick="var s=$('#upimg_sstr').val();jrUpimg_widget_load_images(1,jrE(s));return false;" class="form_button" value="{jrCore_lang module="jrCore" id="8" default="Search"}">
                <input type="button" onclick="jrUpimg_widget_load_images(1);" class="form_button" value="Show All Images">
            </div>
        </td>
    </tr>

    <tr>
        <td class="element_left search_area_left">
            {jrCore_lang module="jrUpimg" id="15" default="Upload Images"}
        </td>
        <td class="element_right search_area_right">
            <div id="pm_upimg_file" class="qq-upload-holder">
                <noscript><p>Please enable JavaScript to use file uploader.</p></noscript>
            </div>
        </td>
    </tr>
</table>

<div id="jrUpimg_holder">
    {jrCore_lang module="jrCore" id="73" default="working..." assign="alt"}
    <div class="p20">{jrCore_image module="jrEmbed" image="spinner.gif" width="24" height="24" alt=$alt}</div>
</div>

{jrCore_module_url module="jrImage" assign="imurl"}
<style type="text/css">
    .qq-upload-spinner {
        background: url("{$jamroom_url}/{$imurl}/img/module/jrCore/loading.gif");
    }
</style>

<script type="text/javascript">
    function jrUpimage_upload_image()
    {
        if ($.isEmptyObject(pm_active_uploads)) {
            $.ajax( {
                type: 'POST',
                data: {
                    upload_token: '{$tkn}'
                },
                cache: false,
                dataType: 'json',
                url: core_system_url + '/{jrCore_module_url module="jrUpimg"}/create_save',
                success: function(m)
                {
                    if (m.success) {
                        var _items = m._items;
                        if ($.isArray(_items)) {
                            $.each(_items, function(key, item)
                            {
                                $('#upimg_results').prepend('<tr class="page_table_row_alt"><td class="page_table_cell center"><a data-lightbox="screens" href="' + core_system_url + '/{$murl}/image/upimg_file/' + item._item_id + '/xxlarge/crop=auto"><img class="img_scale" alt="" src="' + core_system_url + '/{$murl}/image/upimg_file/' + item._item_id + '/small/crop=auto"></a></td><td class="page_table_cell"><h3>' + item.upimg_file_name + '</h3></td><td class="page_table_cell center">@' + item.profile_name + '</td><td class="page_table_cell"><input type="checkbox" onclick="jrUpimg_include(\'' + item._item_id + '\')" title="Add to Image List" value="" class="form_checkbox" id="upimg_id_' + item._item_id + '" checked="checked"></td></tr>');
                                jrUpimg_include(item._item_id);
                            });
                        }
                        $('.qq-upload-list').html('');
                    }
                    else {
                        jrCore_alert('uploaded file was not saved. ' + m.success_msg);
                    }
                }
            } );
        }
    }

    var pm_active_uploads = {ldelim}{rdelim};

    $(document).ready(function()
    {
        var pm_upimg_file = new qq.FileUploader( {
            element: document.getElementById('pm_upimg_file'),
            action: '{$jamroom_url}/core/upload_file/',
            inputName: 'pm_upimg_file',
            acceptFiles: 'png,jpg,gif,jpeg',
            sizeLimit: {$_user['quota_jrCore_max_upload_size']},
            multiple: true,
            debug: false,
            params: {
                upload_name: 'upimg_file',
                field_name: 'pm_upimg_file',
                upload_token: '{$tkn}',
                extensions: 'png,jpg,gif,jpeg',
                multiple: 'true'
            },
            uploadButtonText: '{jrCore_lang module="jrUpimg" id="14" default="Select image(s) to upload."}',
            cancelButtonText: '{jrCore_lang module="jrCore" id="2" default="cancel"}',
            failUploadText: 'upload failed',
            onUpload: function(id, f)
            {
                pm_active_uploads[f] = 1;
                $('.form_submit_section input').attr("disabled", "disabled").addClass('form_button_disabled');
            },
            onComplete: function(id, f, r)
            {
                delete pm_active_uploads[f];
                var c = 0;
                for (var i in pm_active_uploads) {
                    if (pm_active_uploads.hasOwnProperty(i)) {
                        c++;
                    }
                }
                if (c === 0) {
                    $('.form_submit_section input').removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                }
                jrUpimage_upload_image();
            }
        });
        return true;
    });
</script>