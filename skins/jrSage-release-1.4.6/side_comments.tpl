{if isset($_items)}
    <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="78" default="Latest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="77" default="Comments"}</div>
    <div class="body_2">
        <div class="side_comments">
            {foreach from=$_items item="item"}
                {jrComment_get_item_name module=$item.comment_module item_id=$item.comment_item_id assign="item_name"}
                <div class="block">
                    <div style="display:table">
                        <div style="display:table-row;">
                            <div class="p5" style="display:table-cell;width:1%;vertical-align:top">
                                <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="xxsmall" crop="auto" alt=$item.user_name title=$item.user_name class="action_item_user_img iloutline"}</a>
                            </div>
                            <div style="display:table-cell;text-align:left;vertical-align:top">
                                <a href="{$jamroom_url}/{$item.profile_url}"><span class="capital bold">{$item.user_name}</span></a> <span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="117" default="Commented on the"}</span> <span class="capital bold">{$_mods[$item['comment_module']]['module_url']}</span>:<br>
                                <a href="{$item.comment_url}"><span class="media_title">{$item.comment_item_title|truncate:25:"...":false}</span></a>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            {/foreach}
        </div>
    </div>
{/if}
