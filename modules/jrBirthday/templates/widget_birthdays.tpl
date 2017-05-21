{if isset($_items)}
    {foreach $_items as $item}
        <div class="row">
            <div class="col3">
                <div class="p10">
                    {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="large" crop="portrait" class="img_scale" style="margin:0" alt="@`$item.user_name`" title="@`$item.user_name`"}
                </div>
            </div>
            <div class="col9 last">
                <div class="p10">
                <a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a>
                <br>
                {if $item.user_birthdate_today == 1}
                    &nbsp;<strong>{jrCore_lang module="jrBirthday" id=7 default="Today"}</strong>
                {else}
                    &nbsp;{$item.user_birthdate_epoch|jrCore_date_format:"%B %e"}
                {/if}
                </div>
            </div>
        </div>
    {/foreach}
{else}
    <div class="item">
        {jrCore_lang module="jrBirthday" id=8 default="No birthdays found"}
    </div>
{/if}
