{if isset($_items)}

    {jrCore_module_url module="jrAction" assign="murl"}
    {foreach from=$_items item="item"}

        <div id="action-item{$item._item_id}" class="action_item_holder">
            <div class="container">
                <div class="row">

                    <div class="col2">
                        <div class="action_item_media">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name class="action_item_user_img img_shadow img_scale"}
                        </div>
                    </div>

                    <div class="col10 last" style="position:relative">

                        <script type="text/javascript">
                            $(function() {
                                var d = $('#action-controls{$item._item_id}');
                                $('#action-item{$item._item_id}').hover(function()
                                {
                                    d.show();
                                }, function()
                                {
                                    d.hide();
                                });
                            });
                        </script>

                        <div id="action-controls{$item._item_id}" class="action_item_delete">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{jrCore_icon icon="link"}</a>
                            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                        </div>

                        <div>

                            <span class="action_item_title"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>

                            <span class="action_item_actions">
                                &bull; {$item._created|jrCore_date_format:"relative"}
                                {if jrUser_is_logged_in() && $_user._user_id != $item._user_id && $item.action_shared_by_user != '1'}
                                    &bull; <a onclick="jrAction_share('jrAction','{$item._item_id}')">{jrCore_lang module="jrAction" id="10" default="Share This"}</a>
                                {/if}
                                {if $_post.module_url == $_user.profile_url && $item.action_shared_by_user == '1'}
                                    &bull; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{jrCore_lang module="jrAction" id="26" default="shared by you"}</a>
                                {elseif $item.action_shared_by_count > 0}
                                    &bull; {jrCore_lang module="jrAction" id="24" default="shared by"} <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{$item.action_shared_by_count} {jrCore_lang module="jrAction" id="25" default="follower(s)"}</a>
                                {/if}

                                {* We do not show comment links on Follower entires *}
                                {if $item.action_module != 'jrFollower'}
                                    {if isset($item.action_original_item_comment_count)}
                                        &bull; <a href="{$item.action_original_item_url}">{jrCore_lang module="jrAction" id="22" default="Comments"}: {$item.action_original_item_comment_count}</a>
                                    {elseif isset($item.action_item_comment_count)}
                                        &bull; <a href="{$item.action_item_url}">{jrCore_lang module="jrAction" id="22" default="Comments"}: {$item.action_item_comment_count}</a>
                                    {/if}
                                {/if}

                            </span>
                            <br>

                            {* Mention *}
                            {if isset($item.action_mode) && $item.action_mode == 'mention'}

                                {$item.action_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:160}

                            {* Shared Action *}
                            {elseif isset($item.action_shared)}

                                {if strlen($item.action_text) > 0}
                                <div class="action_item_text">
                                    {$item.action_text|jrCore_format_string:$item.profile_quota_id}
                                </div>
                                {/if}

                                {if strlen($item.action_original_html) > 0}
                                <div class="action_item_shared">
                                    {$item.action_original_html}
                                </div>
                                {/if}

                            {* Activity Update *}
                            {elseif $item.action_module == 'jrAction' && isset($item.action_text)}

                                <div class="action_item_text">
                                    {$item.action_text|jrCore_format_string:$item.profile_quota_id}
                                </div>

                            {* Module Actions *}
                            {elseif isset($item.action_html)}

                                {$item.action_html}

                            {/if}

                        </div>
                    </div>

                </div>
            </div>
        </div>

    {/foreach}
{/if}