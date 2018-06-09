{* ROW TEMPLATE *}
{if isset($_items)}

<div class="border-1px" style="display:table;width:100%;">
    <div style="display:table-row;">
        <div class="table-title" style="display:table-cell;text-align:center;padding:4px;border-bottom:1px solid #DEDEDE;border-right:1px solid #DEDEDE">Rnk</div>
        <div class="table-title" style="display:table-cell;text-align:left;padding:4px;border-bottom:1px solid #DEDEDE;border-right:1px solid #DEDEDE">Song</div>
        <div class="table-title" style="display:table-cell;text-align:center;padding:4px;border-bottom:1px solid #DEDEDE;">Play</div>
    </div>
    {jrCore_module_url module="jrAudio" assign="murl"}
    {foreach from=$_items item="item"}
        <div class="body_2" style="display:table-row;">
            <div style="display:table-cell;text-align:center;vertical-align:middle;padding:4px;font-size:15px;{if $item@last}border-right:1px solid #DEDEDE;{else}border-right:1px solid #DEDEDE;border-bottom:1px solid #DEDEDE;{/if}"><span class="hl-1">{$item.list_rank}</span></div>
            <div style="display:table-cell;text-align:left;vertical-align:top;padding:4px;font-size:11px;{if $item@last}border-right:1px solid #DEDEDE;{else}border-right:1px solid #DEDEDE;border-bottom:1px solid #DEDEDE;{/if}">
                <span class="capital hl-2">{$item.audio_title|truncate:25:"...":false}</span><br>
                <a href="{$jamroom_url}/{$item.profile_url}"><span class="capital">{$item.profile_name|truncate:25:"...":false}</span></a>
                <div class="right">
                    <span class="bold" style="font-size:9px;">Plays:</span> <span class="hl-3" style="font-size:9px;">{$item.audio_file_stream_count}</span>
                </div>
            </div>
            <div style="display:table-cell;width:1%;text-align:center;vertical-align:middle;padding:4px;{if $item@last}{else}border-bottom:1px solid #DEDEDE;{/if}">
                {if $item.audio_file_extension == 'mp3'}
                    {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="button_player"}
                {else}
                    {jrCore_lang skin=$_conf.jrCore_active_skin id="156" default="Download" assign="alttitle"}
                    <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                {/if}
            </div>
        </div>

    {/foreach}
</div>

    {if $info.total_pages > 1}

    <div style="float:left;padding-top:8px;">
        {if $info.prev_page > 0}
            <span class="button-arrow-previous" onclick="jrLoad('#side_charts','{$jamroom_url}/side_charts/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#ttcharts').offset().top -100 }, 'slow');return false;">&nbsp;</span>
            {else}
            <span class="button-arrow-previous-off">&nbsp;</span>
        {/if}
        {if $info.next_page > 1}
            <span class="button-arrow-next" onclick="jrLoad('#side_charts','{$jamroom_url}/side_charts/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#ttcharts').offset().top -100 }, 'slow');return false;">&nbsp;</span>
            {else}
            <span class="button-arrow-next-off">&nbsp;</span>
        {/if}
    </div>

    <div style="float:right; padding-top:9px;">
        <a href="{$jamroom_url}/music_charts" title="More Artists"><div class="button-more">&nbsp;</div></a>
    </div>

    <div class="clear"> </div>
    <div class="spacer20"> </div>
    {/if}
{/if}
