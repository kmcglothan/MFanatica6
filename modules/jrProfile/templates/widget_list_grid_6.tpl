<div class="container">
    {if isset($_items)}
        {foreach from=$_items item="item"}

            {if $item@first || ($item@iteration % 6) == 1}
                <div class="row">
            {/if}
            <div class="col2{if $item@last || ($item@iteration % 6) == 0} last{/if}">
                 <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="large" crop="portrait" class="img_scale" style="margin:0" alt="@`$item.profile_url`" title="@`$item.profile_url`"}</a>
            </div>
            {if $item@last || ($item@iteration % 6) == 0}
                </div>
            {/if}

        {/foreach}
    {/if}
</div>
