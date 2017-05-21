{jrCore_module_url module="jrGroup" assign="murl"}
{if isset($_items)}
{foreach $_items as $item}

    {if $item@first || ($item@iteration % 6) == 1}
    <div class="row">
    {/if}

    {if ($item@iteration % 6) === 0}
        <div class="col2 last">
    {else}
        <div class="col2">
    {/if}

        <div class="p5 center" style="position:relative">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.group_title_url}">{jrCore_module_function function="jrImage_display" module="jrGroup" type="group_image" item_id=$item._item_id size="large" crop="auto" class="img_scale" width=false height=false alt="{$item.group_title|jrCore_entity_string}" title="{$item.group_title|jrCore_entity_string}"}</a><br><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.group_title_url}">{$item.group_title}</a>
        </div>

        </div>

    {if ($item@iteration % 6) === 0 || $item@last}
    <div style="clear:both"></div>
    </div>
    {/if}

{/foreach}
{/if}
