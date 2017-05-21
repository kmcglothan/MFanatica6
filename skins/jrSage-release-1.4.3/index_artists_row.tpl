{if isset($_items)}
    <div class="container">
        {foreach from=$_items item="row"}
            {if $row@first || ($row@iteration % 4) == 1}
                <div class="row">
            {/if}
            <div class="col3{if $row@last || ($row@iteration % 4) == 0} last{/if}">
                <div class="center p5 m5" style="padding-top:20px;padding-bottom:20px;">
                    <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="medium" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_shadow"}</a><br>
                    <a href="{$jamroom_url}/{$row.profile_url}" title="{$row.profile_name}"><b>{if strlen($row.profile_name) > 20}{$row.profile_name|truncate:20:"...":false}{else}{$row.profile_name}{/if}</b></a>
                </div>
            </div>
            {if $row@last || ($row@iteration % 4) == 0}
                </div>
            {/if}
        {/foreach}
    </div>
{/if}
