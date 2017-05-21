<div class="attachment_upload_button">
{if $icon}
    {jrCore_icon icon=$icon id="pm_{$field}"}
{else}
    <div id="pm_{$field}" class="{$module}_upload_button"></div>
{/if}
<input type="hidden" name="upload_token" value="{$upload_token}">
<input type="hidden" name="upload_field" value="{$field}">
<input type="hidden" name="upload_module" value="{$module}">

{jrCore_module_url module="jrCore" assign="curl"}
<script type="text/javascript">
$(document).ready(function() {
    var pm_{$field}_au = {ldelim}{rdelim};
    new qq.FileUploader({
        element: document.getElementById('pm_{$field}'),
        action: '{$jamroom_url}/{$curl}/upload_file/',
        inputName: 'pm_{$field}',
        acceptFiles: '{$allowed}',
        sizeLimit: {$_user.quota_jrCore_max_upload_size},
        multiple: {$multiple},
        debug: false,
        params: { upload_name: '{$field}', field_name: 'pm_{$field}', upload_token: '{$upload_token}', extensions: '{$allowed}', multiple: '{$multiple}' },
        uploadButtonText: '{$upload_text}',
        cancelButtonText: '{$cancel_text}',
        failUploadText: 'upload failed',
        onUpload: function (id, f) {
            pm_{$field}_au[f] = 1;
            $('.form_submit_section input').attr("disabled", "disabled").addClass('form_button_disabled');
        },
        onComplete: function (id, f, r) {
            delete pm_{$field}_au[f];
            var count = 0;
            for (var i in pm_{$field}_au) {
                if (pm_{$field}_au.hasOwnProperty(i)) {
                    count++;
                }
            }
            if (count === 0) {
                $('.form_submit_section input').removeAttr("disabled", "disabled").removeClass('form_button_disabled');
                {if strlen($oncomplete) > 0}{$oncomplete}{/if}
            }
        }
    });
    return true;
});
</script>
</div>
