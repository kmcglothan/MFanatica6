{jrCore_module_url module="jrImage" assign="murl"}
<style type="text/css">
    .qq-upload-spinner {
        background: url("{$jamroom_url}/{$murl}/img/module/jrCore/loading.gif");
    }
</style>

<script type="text/javascript">
    function jrUpimage_upload_image(fileName) {
        var alt_text =  fileName.replace(/[^a-zA-Z 0-9 _.]+/g,'');
        $.ajax({
            type: 'POST',
            data: { upload_token: '{$upload_token}' },
            cache: false,
            dataType: 'json',
            url: core_system_url + '/{jrCore_module_url module="jrUpimg"}/create_save',
            success: function(_msg) {
                if (_msg.success) {
                    var ed = top.tinymce.activeEditor, dom = ed.dom;
                    var s = $('#image-size').val();
                    var p = $('#imgpos').val();
                    var m = $('#imgmar').val();
                    switch (p) {
                        case 'stretch':
                            p = 'width:100%;';
                            break;
                        case 'left':
                            p = 'float:left;';
                            break;
                        case 'right':
                            p = 'float:right;';
                            break;
                        case 'normal':
                            p = '';
                            break;
                    }
                    switch (m) {
                        case 0:
                            m = '';
                            break;
                        default:
                            m = 'margin:' + m + 'px;';
                            break;
                    }
                    ed.insertContent(dom.createHTML('img', {
                        src: _msg.image_url + '/' + s,
                        style: p + m,
                        alt: alt_text
                    }));
                    ed.windowManager.close();
                }
                else {
                    alert('uploaded file was not saved. ' + _msg.success_msg);
                }
            }
        });
    }

    {jrCore_module_url module="jrCore" assign="curl"}
    $(document).ready(function() {
        var pm_active_uploads = {ldelim}{rdelim};
    var pm_upimg_file = new qq.FileUploader({
        element: document.getElementById('pm_upimg_file'),
        action: '{$jamroom_url}/{$curl}/upload_file/',
        inputName: 'pm_upimg_file',
        acceptFiles: 'png,jpg,gif,jpeg',
        sizeLimit: {$_user['quota_jrCore_max_upload_size']},
        multiple: false,
        debug: false,
        params: {
            upload_name: 'upimg_file',
            field_name: 'pm_upimg_file',
            upload_token: '{$upload_token}',
            extensions: 'png,jpg,gif,jpeg',
            multiple: 'false'
        },
        uploadButtonText: '{jrCore_lang module="jrUpimg" id="2" default="Select an image to upload and insert"}',
        cancelButtonText: '{jrCore_lang module="jrCore" id="2" default="cancel"}',
        failUploadText: 'upload failed',
        onUpload: function(id, fileName) {
            pm_active_uploads[fileName] = 1;
            $('.form_submit_section input').attr("disabled", "disabled").addClass('form_button_disabled');
        },
        onComplete: function(id, fileName, response) {
            delete pm_active_uploads[fileName];
            var count = 0;
            for (var i in pm_active_uploads) {
                if (pm_active_uploads.hasOwnProperty(i)) {
                    count++;
                }
            }
            if (count === 0) {
                $('.form_submit_section input').removeAttr("disabled", "disabled").removeClass('form_button_disabled');
            }
            jrUpimage_upload_image(fileName);
        }
    });
    return true;
    })
    ;
</script>

<div class="container">

    <div class="row page_table_row">
        <div class="col3">
            <div class="page_table_header">{jrCore_lang module="jrUpimg" id=4 default="size"}</div>
            <div class="page_table_cell center">
                {$imgsize = jrCore_get_cookie('imgsizg')}

                <select id="image-size" class="form_select" style="width:auto"  onchange="jrSetCookie('imgsizg', JSON.stringify($(this).val()));">
                    {foreach $image_sizes as $pixels}
                        {if strlen($image_names[$pixels]) > 2}
                            {$name = " - `$image_names[$pixels]`"}
                        {else}
                            {$name = ''}
                        {/if}

                        {if isset($imgsize) && $imgsize == $pixels}
                            <option value="{$pixels}" selected="selected">{$pixels}px  {$name}</option>
                        {elseif !isset($imgsize) && $pixels == 256}
                            <option value="{$pixels}" selected="selected">{$pixels}px  {$name}</option>
                        {else}
                            <option value="{$pixels}">{$pixels}px  {$name}</option>
                        {/if}
                    {/foreach}
                    {if !isset($_conf.jrImage_block_original_size) || $_conf.jrImage_block_original_size == "off"}
                        <option value="original">original</option>
                    {/if}
                </select>
            </div>

        </div>
        <div class="col2">
            <div class="page_table_header">{jrCore_lang module="jrUpimg" id=6 default="position"}</div>
            <div class="page_table_cell center">
                {$imgposg = jrCore_get_cookie('imgposg')}

                <select id="imgpos" class="form_select" style="width:auto" onchange="jrSetCookie('imgposg', JSON.stringify($(this).val()));">
                    <option value="normal" {if $imgposg == "normal"}selected="selected"{/if}>{jrCore_lang module="jrUpimg" id="8" default="normal"}</option>
                    <option value="left" {if $imgposg == "left" || $imgposg == ""}selected="selected"{/if} >{jrCore_lang module="jrUpimg" id="9" default="float left"}</option>
                    <option value="right" {if $imgposg == "left"}selected="selected"{/if}>{jrCore_lang module="jrUpimg" id="10" default="float right"}</option>
                    <option value="stretch" {if $imgposg == "left"}selected="selected"{/if}>{jrCore_lang module="jrUpimg" id="12" default="stretch"}</option>
                </select>
            </div>
        </div>
        <div class="col2">
            <div class="page_table_header">{jrCore_lang module="jrUpimg" id=17 default="margin"}</div>
            <div class="page_table_cell center">
                {$imgposg = jrCore_get_cookie('imgmarg')}

                <select id="imgmar" class="form_select" style="width:auto" onchange="jrSetCookie('imgmarg', JSON.stringify($(this).val()));">
                    <option value="0" {if $imgposg == "0"}selected="selected"{/if}>none</option>
                    <option value="1" {if $imgposg == "1"}selected="selected"{/if}>1px</option>
                    <option value="2" {if $imgposg == "2"}selected="selected"{/if}>2px</option>
                    <option value="3" {if $imgposg == "3"}selected="selected"{/if}>3px</option>
                    <option value="4" {if $imgposg == "4"}selected="selected"{/if}>4px</option>
                    <option value="5" {if $imgposg == "5"}selected="selected"{/if}>5px</option>
                    <option value="6" {if $imgposg == "6"}selected="selected"{/if}>6px</option>
                    <option value="8" {if $imgposg == "8"}selected="selected"{/if}>8px</option>
                    <option value="10" {if $imgposg == "10"}selected="selected"{/if}>10px</option>
                    <option value="12" {if $imgposg == "12"}selected="selected"{/if}>12px</option>
                    <option value="15" {if $imgposg == "15"}selected="selected"{/if}>15px</option>
                    <option value="18" {if $imgposg == "18"}selected="selected"{/if}>18px</option>
                    <option value="20" {if $imgposg == "20"}selected="selected"{/if}>20px</option>
                </select>
            </div>
        </div>
        <div class="col5">
            <div class="page_table_header">{jrCore_lang module="jrUpimg" id=16 default="image"}</div>
            <div class="page_table_cell center">
                <div id="pm_upimg_file" class="qq-upload-holder">
                    <noscript><p>Please enable JavaScript to use file uploader.</p></noscript>
                </div>
            </div>
        </div>

    </div>

</div>

