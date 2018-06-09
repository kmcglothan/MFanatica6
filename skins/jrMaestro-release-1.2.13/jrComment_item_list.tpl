{jrCore_module_url module="jrComment" assign="murl"}
{foreach $_items as $item}

{if $_conf.jrComment_threading == 'on' && isset($item.comment_thread_level) && $item.comment_thread_level > 0}
    {if $item.comment_thread_level > 7}
    <div id="cm{$item._item_id}" class="comment-level-last">
    {else}
       <div id="cm{$item._item_id}" class="comment-level-{$item.comment_thread_level}">
    {/if}
{else}
    <div id="cm{$item._item_id}" class="comment-level-0">
{/if}

            <div class="comment_content">
                <div class="user_image">
                    {jrCore_module_function
                    function="jrImage_display"
                    module="jrUser"
                    type="user_image"
                    item_id=$item._user_id
                    size="small" alt=$item.user_name
                    crop="auto"
                    class="img_scale"}
                </div>
                <div class="comment" id="c{$item._item_id}" style="position: relative;">
                    <span class="comment_name"><a
                                href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></span>
            <span class="comment_text">
                 {if isset($_conf.jrComment_editor) && $_conf.jrComment_editor == 'on'}
                     {$item.comment_text|jrCore_format_string:$item.profile_quota_id|regex_replace:"/(<p>|<p [^>]*>|<\\/p>)/":""}
                 {else}
                     {$item.comment_text|jrCore_format_string:$item.profile_quota_id:null:"html"|regex_replace:"/(<p>|<p [^>]*>|<\\/p>)/":""}
                 {/if}

                {if jrUser_is_logged_in()}
                    <div id="bc{$item._item_id}" style="position:absolute;top:5px;right:5px;display:none">
                        <script>$(function () {
                                $('#cm{$item._item_id}').hover(function () {
                                    $('#bc{$item._item_id}').toggle();
                                });
                            });</script>

                        {if $item.comment_locked != 'on'}
                            {if isset($_conf.jrComment_quote_button) && $_conf.jrComment_quote_button == 'on'}
                                {if isset($_conf.jrComment_editor) && $_conf.jrComment_editor == 'on'}
                                    <a onclick="jrCommentEditorQuotePost({$item._item_id});"
                                       title="{jrCore_lang module="jrComment" id="26" default="quote this"}">{jrCore_icon icon="quote"}</a>
                                {else}
                                    <a onclick="jrCommentQuotePost({$item._item_id});"
                                       title="{jrCore_lang module="jrComment" id="26" default="quote this"}">{jrCore_icon icon="quote"}</a>
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
{if isset($_conf.jrComment_quote_button) && $_conf.jrComment_quote_button == 'on'}
{* do not indent this or the quoting looks funny *}
{if jrUser_is_logged_in()}
<div id="q{$item._item_id}" style="display:none">[quote="{$item.user_name}"]
{if isset($_conf.jrComment_editor) && $_conf.jrComment_editor == 'on'}{$item.comment_text|trim}{else}{$item.comment_text|trim|htmlentities}{/if}
[/quote]
</div>
{/if}
{/if}

                {* Attachments *}
                {jrCore_get_uploaded_files module="jrComment" item=$item field="comment_file"}


            </span>
                    <br>
                    <div>
                        {if jrUser_is_logged_in() && $_conf.jrComment_threading == 'on'}
                            {jrLike_button action="like" module="jrComment" item=$item}
                            &nbsp;&nbsp;

                            <a href="#" onclick="jrMaestro_reply_to({$item._item_id}, '{$item.user_name|addslashes}','#{$item.comment_module}_{$item.comment_item_id}_comments' )">
                                {jrCore_lang skin="jrMaestro" id=80 default="Reply"}</a>
                            &nbsp;&nbsp;
                        {/if}

                        <span class="time">{$item._created|jrCore_date_format:"relative"}</span>
                    </div>
                    <div id="r{$item._item_id}" style="display:none; width: 100%">
                        {* comment form will load here *}
                    </div>
                </div>

            </div>
        </div>
        {/foreach}
