{if isset($_post.option) && $_post.option == 'alpha'}
    {assign var="jrload_div" value="alpha_members"}
    {assign var="scroll_anchor" value="amembers"}
{elseif isset($_post.option) && $_post.option == 'newest'}
    {assign var="jrload_div" value="new_members"}
    {assign var="scroll_anchor" value="nmembers"}
{elseif isset($_post.option) && $_post.option == 'most_viewed'}
    {assign var="jrload_div" value="most_viewed_members"}
    {assign var="scroll_anchor" value="mvmembers"}
{/if}
{if isset($_items)}
<div class="container">

    {foreach from=$_items item="item"}
    {if $item@first || ($item@iteration % 4) == 1}
    <div class="row">
    {/if}
        <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
            <div class="center mb10">
                <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" class="iloutline img_scale" alt=$item.profile_name title=$item.profile_name style="max-width:182px;"}</a><br>
                <a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}"><span class="capital bold">{if strlen($item.profile_name) > 15}{$item.profile_name|truncate:15:"...":false}{else}{$item.profile_name}{/if}</span></a>
                {* if jrUser_is_admin() || jrUser_is_master()}
                    &nbsp;{jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$item._profile_id`" profile_id=$item._profile_id item_id=$item._profile_id title="Update Profile"}<br>
                {else}
                    <br>
                {/if *}
                {if isset($_post.option) && $_post.option == 'newest'}
                    {jrCore_lang skin=$_conf.jrCore_active_skin id="122" default="Joined"}: {$item._created|date_format:"%A<br>%B %e, %Y<br>%I:%M:%S %p"}
                {elseif isset($_post.option) && $_post.option == 'most_viewed'}
                    Views({$item.profile_view_count})
                {/if}
            </div>
        </div>
    {if $item@last || ($item@iteration % 4) == 0}
    </div>
    {/if}
    {/foreach}
</div>
    {if $info.total_pages > 1}
        {if $info.page == '1'}

            <div class="float-right"><a onclick="jrLoad('#{$jrload_div}','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#{$scroll_anchor}').offset().top -100 }, 'slow');"><div class="button-more">&nbsp;</div></a></div>
            <div class="clear"></div>

        {else}

            <div class="block">
                <table style="width:100%;">
                    <tr>

                        <td class="body_5 page" style="width:25%;text-align:center;">
                            {if isset($info.prev_page) && $info.prev_page > 0}
                                <a onclick="jrLoad('#{$jrload_div}','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#{$scroll_anchor}').offset().top -100 }, 'slow');"><span class="button-arrow-previous">&nbsp;</span></a>
                            {else}
                                <span class="button-arrow-previous-off">&nbsp;</span>
                            {/if}
                        </td>

                        <td class="body_5" style="width:50%;text-align:center;">
                            {if $info.total_pages <= 5 || $info.total_pages > 500}
                                {$info.page} &nbsp;/ {$info.total_pages}
                            {else}
                                <form name="form" method="post" action="_self">
                                    <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#{$jrload_div}','{$info.page_base_url}/p=' +sel);$('html, body').animate({ scrollTop: $('#{$scroll_anchor}').offset().top -100 }, 'slow');">
                                        {for $pages=1 to $info.total_pages}
                                            {if $info.page == $pages}
                                                <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                                {else}
                                                <option value="{$pages}"> {$pages}</option>
                                            {/if}
                                        {/for}
                                    </select>&nbsp;/&nbsp;{$info.total_pages}
                                </form>
                            {/if}
                        </td>

                        <td class="body_5 page" style="width:25%;text-align:center;">
                            {if isset($info.next_page) && $info.next_page > 1}
                                <a onclick="jrLoad('#{$jrload_div}','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#{$scroll_anchor}').offset().top -100 }, 'slow');"><span class="button-arrow-next">&nbsp;</span></a>
                            {else}
                                <span class="button-arrow-next-off">&nbsp;</span>
                            {/if}
                        </td>

                    </tr>
                </table>
            </div>
        {/if}
    {/if}
{/if}
