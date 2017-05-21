{if isset($_items)}
<div class="row">
    {foreach from=$_items item="row"}
        <div class="col2{if $row@last} last{/if} center">
            <div class="center m0 p8">
                <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="large" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline img_scale" style="max-width:175px;max-height:111px;"}</a>
                <br>
                <a href="{$jamroom_url}/{$row.profile_url}"><span class="media_title">{$row.profile_name}</span></a>
            </div>
        </div>
  {/foreach}
</div>
{/if}
