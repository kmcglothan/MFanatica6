<div class="container">
    {if isset($_items)}
        {jrCore_module_url module="jrSoundCloud" assign="murl"}
        {foreach from=$_items item="item"}

            {if $item@first || ($item@iteration % 12) == 1}
                <div class="row">
            {/if}
            <div class="col1{if $item@last || ($item@iteration % 12) == 0} last{/if}">
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">
                    <img src="{$item.soundcloud_artwork_url}" title="@{$item.profile_url}: {$item.soundcloud_title|jrCore_entity_string}" alt="{$item.soundcloud_title|jrCore_entity_string}" class="iloutline img_scale">
                </a>
            </div>
            {if $item@last || ($item@iteration % 12) == 0}
                </div>
            {/if}

        {/foreach}
    {/if}
</div>
