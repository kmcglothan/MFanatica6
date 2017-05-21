{if isset($_items)}
    <div class="title">
        <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="78" default="Latest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="77" default="Comments"}</h2>
    </div>
    <div class="body_3 mb10 side_comments">
        {foreach from=$_items item="item"}
            {jrComment_get_item_name module=$item.comment_module item_id=$item.comment_item_id assign="item_name"}
            <div class="block">
                <div style="display:table">
                    <div style="display:table-row;">
                        <div class="p5" style="display:table-cell;width:1%;vertical-align:top">
                            <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="xxsmall" crop="auto" alt=$item.user_name title=$item.user_name class="action_item_user_img iloutline"}</a>
                        </div>
                        <div style="display:table-cell;text-align:left;vertical-align:top">
                            <span class="capital">{$item.user_name} {jrCore_lang module="jrComment" id="3" default="Commented on"} {$_mods[$item['comment_module']]['module_url']} {jrCore_lang module="jrComment" id="4" default="item"}:</span><br>
                            <a href="{$item.comment_url}"><span class="media_title">{$item.comment_item_title|truncate:25:"...":false}</span></a>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        {/foreach}
    </div>
{/if}
