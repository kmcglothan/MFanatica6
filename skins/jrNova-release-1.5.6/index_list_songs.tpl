{if isset($_items)}
  {foreach from=$_items item="item"}
  <div style="display:table">
      <div style="display:table-row">
          <div style="display:table-cell">
              <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="small" crop="auto" alt=$item.audio_title title=$item.audio_title class="iloutline" width=false height=false}</a>
          </div>
          <div class="media_title p5" style="display:table-cell;vertical-align:middle">
              <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}" title="{$item.audio_title}">{if strlen($item.audio_title) > 25}{$item.audio_title|truncate:25:false}{else}{$item.audio_title}{/if}</a><br>
              {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
          </div>
      </div>
  </div>
  {/foreach}
    {if $info.total_pages > 1 && $info.page == 1}
    <div class="center p10">
        <input type="button" value="{jrCore_lang module="jrNova" id="41" default="more"} {jrCore_lang module="jrNova" id="31" default="top"} {jrCore_lang module="jrNova" id="13" default="songs"}" class="form_button" onclick="jrLoad('#top_songs','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#tsongs').offset().top -100 }, 'slow');return false;">
    </div>
        {elseif $info.page >= 2}
    <div class="block">
        <table style="width:100%;">
            <tr>

                <td style="width:25%;">
                    {if isset($info.prev_page) && $info.prev_page > 0}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrLoad('#top_songs','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#tsongs').offset().top -100 }, 'slow');return false;">
                    {/if}
                </td>

                <td style="width:50%;text-align:center;">
                    {if $info.total_pages <= 5}
                        {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                        <form name="form" method="post" action="_self">
                            <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#top_songs','{$info.page_base_url}/p=' +sel);$('html, body').animate({ scrollTop: $('#tsongs').offset().top -100 }, 'slow');">
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
                        <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrLoad('#top_songs','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#tsongs').offset().top -100 }, 'slow');return false;">
                    {/if}
                </td>

            </tr>
        </table>
    </div>
    {/if}
{/if}