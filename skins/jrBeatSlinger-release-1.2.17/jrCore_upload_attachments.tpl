{jrCore_module_url module="jrCore" assign="murl"}
{jrCore_module_url module=$upload_module assign="imurl"}
{* Check for attachments *}
{if isset($item.attachments)}
    <div class="attachment_box">
        {foreach $item.attachments as $att}
            <div id="{$upload_module}_{$item._item_id}_{$att.idx}" class="p5 m5 center normal image_update_display" onmouseover="$('#d{$item._item_id}_{$att.idx}').show()" onmouseout="$('#d{$item._item_id}_{$att.idx}').hide()">
                {* If this is an image, we can show it inline *}
                {if $att.type == 'image'}
                    <a href="{$jamroom_url}/{$imurl}/image/{$att.field}/{$item._item_id}/1280" data-lightbox="images" title="{$att.name|jrCore_entity_string}">
                        {jrCore_module_function
                            function="jrImage_display"
                            module=$upload_module
                            type=$att.field
                            item_id=$item._item_id
                            size="large"
                            crop="3:2"
                            width="false"
                            alt=$att.name
                        }</a>
                    <br>
                    <a href="{$jamroom_url}/{$imurl}/download/{$att.field}/{$item._item_id}">download</a>
                    <br>
                    {$att.size|jrCore_format_size}
                {else}
                    <div style="height:64px;width:64px;padding:16px;border:1px solid #EEE;">
                        <a href="{$jamroom_url}/{$imurl}/download/{$att.field}/{$item._item_id}" title="{$att.name|jrCore_entity_string}">
                            {if is_file("`$jamroom_dir`/modules/jrCore/img/`$att.extension`.png")}
                                {jrCore_image module="jrCore" image="`$att.extension`.png" width="32" alt=$att.extension style="vertical-align:middle"}
                            {else}
                                {jrCore_image module="jrCore" image="_blank.png" width="32" alt=$att.extension style="vertical-align:middle"}
                            {/if}
                                <br>
                                download
                        </a><br>{$att.size|jrCore_format_size}
                    </div>
                {/if}
                {if jrUser_is_admin() || jrProfile_is_profile_owner($_profile_id) || $item._user_id == $_user._user_id}
                    {jrCore_lang module="jrCore" id="91" default="Delete this attachment?" assign="prompt"}
                    <div id="d{$item._item_id}_{$att.idx}" class="image_delete">
                        <a onclick="if(confirm('{$prompt|jrCore_entity_string}')) { jrCore_delete_attachment('{$item._item_id}','{$att.field}','{$upload_module}', '{$att.idx}')}">{jrCore_icon icon="close" size="16"}</a>
                    </div>
                {/if}
            </div>
        {/foreach}
        <div style="clear:both"></div>

    </div>
{/if}