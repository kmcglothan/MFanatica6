{if isset($_items)}
    {jrCore_module_url module="jrForum" assign="murl"}
    {foreach from=$_items item="item"}
        <div class="table forum">
            <div class="table-item">
                <div class="table-cell" style="width:50px;">
                    {jrCore_module_function function="jrImage_display" module='jrUser' type='user_image' item_id=$item._user_id size="large" crop="auto" class="img_scale" alt=$item.profile_name width=false height=false}
                </div>
                <div class="table-cell">
                    <h3><a href="{$item.forum_topic_url}" class="media_title">{$item.forum_title}</a></h3><br>
                    <small>{jrCore_lang skin="jrElastic2" id=9 default="by"} {$item.user_name} &middot; {$item._created|jrCore_date_format:"relative"}</small>
                </div>
            </div>
        </div>
    {/foreach}
{/if}
