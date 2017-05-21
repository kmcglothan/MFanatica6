{if isset($_items)}
    {foreach $_items as $item}

        {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="icon" crop="auto" alt=$item.profile_image_name} <br>

    {/foreach}
{/if}