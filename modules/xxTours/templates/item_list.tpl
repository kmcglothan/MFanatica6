{jrCore_module_url module="xxTours" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="item">

            <div class="block_config">
                {jrCore_item_list_buttons module="xxTours" item=$item}
            </div>
            <h2>{$item.profile_name} - <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/artist_tourmap?id={$item._item_id}">{$item.tours_title}</a></h2> : <h4>{$item.tours_desc}</h4>
            {*<h2>{$item.profile_name} - <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.tours_title_url}">{$item.tours_title}</a></h2> : <h4>{$item.tours_desc}</h4>*}
            <br>
        </div>

    {/foreach}
{/if}
