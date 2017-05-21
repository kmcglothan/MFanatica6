{jrCore_module_url module="jrFollower" assign="murl"}
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
            <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="large" crop="auto" class="img_scale" width=false height=false alt="{$txt|jrCore_entity_string}" title="{$txt|jrCore_entity_string}"}</a><br><a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a><br>
        </div>

        </div>

    {if ($item@iteration % 6) === 0 || $item@last}
    <div style="clear:both"></div>
    </div>
    {/if}

{/foreach}
{/if}
