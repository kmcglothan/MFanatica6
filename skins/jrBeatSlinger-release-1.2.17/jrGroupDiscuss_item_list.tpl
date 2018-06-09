{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}
{if isset($_items)}
{$_items = $_items|jrGroupDiscuss_add_group_url}
    {foreach $_items as $item}
        <div class="item">
            <div class="row">
                <div class="col2">
                    <div style="padding:3px 12px 3px 0">
                        <a href="{$jamroom_url}/{$item.group_profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="large" crop="auto" class="img_scale" alt=$item.user_name width=false height=false _v=$item._updated}</a>
                    </div>
                </div>
                <div class="col10 last">
                    {if jrUser_is_logged_in()}
                        <div class="p5" style="float: right;">
                            {if $item.discuss_user_is_following == '1'}
                                {jrCore_module_function function="jrGroupDiscuss_follow_button" icon="site-hilighted" item_id=$item._item_id}
                            {else}
                                {jrCore_module_function function="jrGroupDiscuss_follow_button" icon="site" item_id=$item._item_id}
                            {/if}
                        </div>
                    {/if}
                    <div class="p5">
                        <h2><a href="{$jamroom_url}/{$item.group_profile_url}/{$murl}/{$item._item_id}/{$item.discuss_title_url}">{$item.discuss_title}</a></h2>
                        <br>{$item.discuss_description|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:150}
                        <br><small><a href="{$jamroom_url}/{$item.home_profile_url}">@{$item.user_name}</a> started {$item._created|jrCore_format_time:false:"relative"} - replies: {$item.discuss_comment_count|default:0}</small>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{/if}
