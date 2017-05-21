{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}
{if isset($_items)}
<div class="item">
<div class="container">
    {foreach $_items as $item}
    <div class="row">
        <div class="col1">
            <div style="padding:3px 12px 3px 0">
            <a href="{$jamroom_url}/{$item._group_data.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon96" crop="auto" class="img_scale" alt=$item.user_name width=false height=false _v=$item._updated}</a>
            </div>
        </div>
        <div class="col11 last">
            <div class="p5">
                <h2><a href="{$jamroom_url}/{$item._group_data.profile_url}/{$murl}/{$item._item_id}/{$item.discuss_title_url}">{$item.discuss_title}</a></h2>
                <br>{$item.discuss_description|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:150}
                <br><small><a href="{$jamroom_url}/{$item._group_data.profile_url}">@{$item.user_name}</a> {jrCore_lang module="jrGroupDiscuss" id=34 default="started"} {$item._created|jrCore_format_time:false:"relative"} - {jrCore_lang module="jrGroupDiscuss" id=35 default="replies"}: {$item.discuss_comment_count|default:0}</small>
            </div>
        </div>
    </div>
    {/foreach}
</div>
</div>
{/if}
