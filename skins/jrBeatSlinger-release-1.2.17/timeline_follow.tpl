{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="list_item spaced" style="padding: 10px">
            <div id="follow_{$item._item_id}" style="display: table;">
                <div class="clearfix">
                    <div style="float: left; width: 25%; margin: 0 10px 0 0">
                        <a href="{$jamroom_url}/{$item.profile_url}">
                            {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" class="img_scale" alt=$item.profile_name title=$item.profile_name width=false height=false}
                        </a>
                    </div>
                    <span><a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url|truncate:20}</a></span><br>
                    <div style="margin-top:6px">{jrFollower_button profile_id=$item._profile_id}</div>
                </div>
            </div>
        </div>

    {/foreach}
{/if}
