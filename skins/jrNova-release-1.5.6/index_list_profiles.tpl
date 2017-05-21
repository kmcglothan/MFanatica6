{if isset($_items)}
  {foreach from=$_items item="row"}
  <div style="display:table">
      <div style="display:table-cell">
          <a href="{$jamroom_url}/{$row.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$row._profile_id size="small" crop="auto" alt=$row.profile_name title=$row.profile_name class="iloutline"}</a>
      </div>
      <div class="media_title p5" style="display:table-cell;vertical-align:middle">
          <a href="{$jamroom_url}/{$row.profile_url}">{$row.profile_name}</a>
      </div>
  </div>
  {/foreach}
{if $info.total_pages > 1 && $info.page == 1}
<div class="center p10">
    <input type="button" value="{jrCore_lang module="jrNova" id="41" default="more"} {jrCore_lang module="jrNova" id="31" default="top"} {jrCore_lang module="jrNova" id="12" default="artists"}" class="form_button" onclick="jrLoad('#top_artists','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#tartists').offset().top -100 }, 'slow');">
</div>
{elseif $info.page >= 2}
<div class="block">
    <table style="width:100%;">
        <tr>

            <td style="width:25%;">
                {if isset($info.prev_page) && $info.prev_page > 0}
                    <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrLoad('#top_artists','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#tartists').offset().top -100 }, 'slow');">
                {/if}
            </td>

            <td style="width:50%;text-align:center;">
                {if $info.total_pages <= 5}
                    {$info.page} &nbsp;/ {$info.total_pages}
                {else}
                    <form name="form" method="post" action="_self">
                        <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#top_artists','{$info.page_base_url}/p=' +sel);$('html, body').animate({ scrollTop: $('#tartists').offset().top -100 }, 'slow');">
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

            <td style="width:25%;text-align:right;">
                {if isset($info.next_page) && $info.next_page > 1}
                    <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrLoad('#top_artists','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#tartists').offset().top -100 }, 'slow');">
                {/if}
            </td>

        </tr>
    </table>
</div>
{/if}
{/if}
