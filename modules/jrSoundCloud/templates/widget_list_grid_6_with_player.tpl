<div class="container">
    {if isset($_items)}
        {foreach from=$_items item="item"}

            {if $item@first || ($item@iteration % 6) == 1}
                <div class="row">
            {/if}
            <div class="col2{if $item@last || ($item@iteration % 6) == 0} last{/if}">
                <a onclick="$('#scplayer{$item._item_id}').modal()">
                    <img src="{$item.soundcloud_artwork_url}" title="@{$item.profile_url}: {$item.soundcloud_title|jrCore_entity_string}" alt="{$item.soundcloud_title|jrCore_entity_string}" class="iloutline img_scale">
                </a>
            </div>
            {* This is the player - shows as a modal window *}
            <div id="scplayer{$item._item_id}" class="search_box" style="width:600px;height:200px;display:none;">
                {jrSoundCloud_embed item_id=$item._item_id auto_play=false width="100%"}
                <div style="float:right;clear:both;margin-top:3px;">
                    <a class="simplemodal-close">{jrCore_icon icon="close" size=20}</a>
                </div>
                <div class="clear"></div>
            </div>

            {if $item@last || ($item@iteration % 6) == 0}
                </div>
            {/if}

        {/foreach}
    {/if}
</div>
