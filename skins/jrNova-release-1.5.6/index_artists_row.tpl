{if isset($_items)}
<div class="container">
    {foreach from=$_items item="row"}
    {if $row@first || ($row@iteration % 4) == 1}
    <div class="row">
    {/if}
        <div class="col3{if $row@last || ($row@iteration % 4) == 0} last{/if}">
            <div class="media_title center p5">
                <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="medium" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow"}</a><br>
                <br><a href="{$jamroom_url}/{$row.profile_url}">{$row.profile_name}</a>
            </div>
        </div>
    {if $row@last || ($row@iteration % 4) == 0}
    </div>
    {/if}
    {/foreach}
</div>
{/if}
