{jrCore_module_url module="jrComment" assign="murl"}
{foreach $_items as $item}

{if $_conf.jrComment_threading == 'on' && isset($item.comment_thread_level) && $item.comment_thread_level > 0}
    {if $item.comment_thread_level > 7}
        <div id="cm{$item._item_id}" class="item comment-level-last">
    {else}
        <div id="cm{$item._item_id}" class="item comment-level-{$item.comment_thread_level}">
    {/if}
{else}
    <div id="cm{$item._item_id}" class="item comment-level-0">
{/if}

    <div class="container">
        <div class="row">
            <div class="col1">
                <div class="block_image p5">
                    {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" crop="portrait" alt=$item.user_name class="action_item_user_img iloutline img_scale" style="max-width:70px;max-height:70px;margin:8px;"}                </div>
            </div>
            <div class="col11 last" style="position:relative">

                <div id="c{$item._item_id}" class="p5" style="margin-left:12px">

                    <span class="info" style="display:inline-block;">{$item._created|jrCore_date_format} <a href="{$jamroom_url}/{$item.profile_url}"><span style="text-transform:lowercase">@{$item.profile_url}</span></a>:</span><br>
                    <span class="normal comment_text">
                    {if isset($_conf.jrComment_editor) && $_conf.jrComment_editor == 'on'}
                        {$item.comment_text|jrCore_format_string:$item.profile_quota_id}
                    {else}
                        {$item.comment_text|jrCore_format_string:$item.profile_quota_id:null:"html"}
                    {/if}
                    </span>

                    {if jrUser_is_logged_in() && $_conf.jrComment_threading == 'on'}
                        <br><a onclick="jrComment_reply_to({$item._item_id}, '{$item.user_name|addslashes}')"><span class="comment-reply">reply</span></a>
                        <div id="r{$item._item_id}" style="display:none">
                            {* comment form will load here *}
                        </div>
                    {/if}

                    <br/>

                    {* Attachments *}
                    {jrCore_get_uploaded_files module="jrComment" item=$item field="comment_file"}

                </div>

                {if jrUser_is_logged_in()}
                <div id="bc{$item._item_id}" class="block_config" style="position:absolute;top:0;right:0;display:none">
                    <script>$(function() { var bc = $('#bc{$item._item_id}'); $('#cm{$item._item_id}').hover(function() { bc.show(); }, function() { bc.hide(); } ); }); </script>

                    {if $item.comment_locked != 'on'}
                        {if isset($_conf.jrComment_quote_button) && $_conf.jrComment_quote_button == 'on'}
                            {if isset($_conf.jrComment_editor) && $_conf.jrComment_editor == 'on'}
                                <a onclick="jrCommentEditorQuotePost({$item._item_id});" title="{jrCore_lang module="jrComment" id=26 default="quote this"}">{jrCore_icon icon="quote"}</a>
                            {else}
                                <a onclick="jrCommentQuotePost({$item._item_id});" title="{jrCore_lang module="jrComment" id=26 default="quote this"}">{jrCore_icon icon="quote"}</a>
                            {/if}
                        {/if}
                     {/if}

                    {if jrUser_is_admin() || !isset($item.comment_locked)}
                        {if $_params.profile_owner_id > 0}
                            {* profile owners can delete comments *}
                            {jrCore_item_update_button module="jrComment" profile_id=$_params.profile_owner_id item_id=$item._item_id}
                            {jrCore_item_delete_button module="jrComment" profile_id=$_params.profile_owner_id item_id=$item._item_id}
                        {else}
                            {* site admins and comment owners see this button *}
                            {jrCore_item_update_button module="jrComment" profile_id=$item._profile_id item_id=$item._item_id}
                            {jrCore_item_delete_button module="jrComment" profile_id=$item._profile_id item_id=$item._item_id}
                        {/if}
                    {/if}

                </div>
                {/if}


            </div>
        </div>
    </div>

</div>
{/foreach}
