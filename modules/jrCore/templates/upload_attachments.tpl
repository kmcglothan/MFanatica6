{jrCore_module_url module="jrCore" assign="murl"}
{jrCore_module_url module=$upload_module assign="imurl"}
{* Check for attachments *}
{if isset($item.attachments)}
    <div id="ab{$item._item_id}" class="file_attachment_box">

        {jrCore_lang module="jrCore" id=91 default="Delete this attachment?" assign="prompt"}
        {jrCore_lang module="jrCore" id=92 default="download" assign="dl"}

        {foreach $item.attachments as $att}

            {assign var="id" value="attachment_`$upload_module`_`$item._item_id`_`$att.idx`"}
            <div id="{$upload_module}_{$item._item_id}_{$att.idx}" class="p5 image_update_display file_attachment" onmouseover="$('#{$id}').show()" onmouseout="$('#{$id}').hide()">

                {* If this is an image, we can show it inline *}
                <div class="file_attachment_image">
                {if $att.type == 'image'}

                    <a href="{$jamroom_url}/{$imurl}/image/{$att.field}/{$item._item_id}/1280?_v={$att.time}" data-lightbox="images" title="{$att.name|jrCore_entity_string}">{jrCore_module_function function="jrImage_display" module=$upload_module type=$att.field item_id=$item._item_id size="small" crop="auto" width=40 class="iloutline" alt=$att.name _v=$att.time}</a>

                {else}

                    {if is_file("`$jamroom_dir`/modules/jrCore/img/file_type_`$att.extension`.png")}
                        {jrCore_image module="jrCore" image="file_type_`$att.extension`.png" width=40 alt=$att.extension class="iloutline" style="vertical-align:middle"}
                    {else}
                        {jrCore_image module="jrCore" image="file_type_unknown.png" width=40 alt=$att.extension class="iloutline" style="vertical-align:middle"}
                    {/if}

                {/if}
                </div>
                <div class="file_attachment_text"><a href="{$jamroom_url}/{$imurl}/download/{$att.field}/{$item._item_id}/{$att.name}" title="{$dl|jrCore_entity_string}">{$att.name}</a> &nbsp;&bull;&nbsp; {$att.size|jrCore_format_size}</div>

                {if jrUser_is_admin() || jrProfile_is_profile_owner($_profile_id) || $item._user_id == $_user._user_id}
                    <div id="{$id}" class="image_delete">
                        <a onclick="jrCore_confirm('{$prompt|jrCore_entity_string}', '', function() { jrCore_delete_attachment('{$item._item_id}','{$att.field}','{$upload_module}','{$att.idx}') } )">{jrCore_icon icon="close" size=16}</a>
                    </div>
                {/if}

            </div>
            <br>
        {/foreach}

    </div>
{/if}