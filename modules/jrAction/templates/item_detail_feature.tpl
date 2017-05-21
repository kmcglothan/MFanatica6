<div class="item">
    {if $show_item_has_been_shared == true}
        <div id="shared_item_status" class="success rounded">
            <span>&#10003; {jrCore_lang module="jrAction" id=35 default="You have shared this with your Followers"}</span>
            {jrCore_lang module="jrAction" id=41 default="view" assign="val"}
            <input id="shared_item_view_button" type="button" value="{$val|jrCore_entity_string}" class="form_button share_view_button" onclick="jrCore_window_location('{$view_url}')">
            <div style="clear:both"></div>
        </div>
    {else}
        <input id="share-to-timeline" type="button" value="{jrCore_lang module="jrAction" id=34 default="Share This with your Followers"}" class="form_button" onclick="jrAction_share('{$module}','{$item._item_id}');">
    {/if}
</div>