{if isset($_items)}
{foreach from=$_items item="item"}
    <div class="small_image">
        <div>
            <a href="{$jamroom_url}/{$item.profile_url}">
                {jrCore_module_function
                function="jrImage_display"
                module="jrUser"
                type="user_image"
                item_id=$item._user_id
                size="small"
                crop="auto"
                class="img_scale"
                alt="{$item.user_name|jrCore_entity_string}"
                title="{$item.user_name|jrCore_entity_string
                }"}

            </a>
        </div>
    </div>
{/foreach}
{/if}
