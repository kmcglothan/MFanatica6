{if isset($_items)}
<div class="container">
    {foreach from=$_items item="item"}
    {if $item@first || ($item@iteration % 4) == 1}
    <div class="row">
    {/if}
        <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
            <div class="p5" style="text-align:center;">
                <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.video_title_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.video_title title=$item.video_title class="iloutline img_shadow"}</a><br>
                <div style="width:196px;margin:0 auto;">
                    <table>
                        <tr>
                            <td class="media_title capital" style="text-align:center;">
                                <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.video_title_url}" title="{$item.video_title}">{$item.video_title|truncate:25:"...":false}</a><br>
                                <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    {if $item@last || ($item@iteration % 4) == 0}
    </div>
    {/if}
    {/foreach}
</div>
{/if}
