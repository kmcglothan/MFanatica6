{capture name="row_template" assign="fan_activity_row"}
    {literal}
    {if isset($_items)}

    {jrCore_module_url module="jrTrace" assign="turl"}
    {foreach from=$_items item="item"}
    <div class="item nowrap">

        {if $item.trace_event == 'stream_file'}
        {jrCore_lang skin=$_conf.jrCore_active_skin id="73" default="Streamed" assign="maction"}
        {else}
        {jrCore_lang skin=$_conf.jrCore_active_skin id="74" default="Downloaded" assign="maction"}
        {/if}

        <div class="table-div">
            <div class="table-row-div">
                <div class="table-cell-div center middle">
                    <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xsmall" crop="auto" alt=$item.profile_name title=$item.profile_name class="iloutline img_shadow"}</a><br>
                </div>
                <div class="table-cell-div left top" style="padding-left:15px;">
                    <span class="normal capital"><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></span>&nbsp;{$maction}&nbsp;
                    {if $item.trace_module == 'jrAudio'}
                    {jrCore_module_url module="jrAudio" assign="murl"}
                    <a href="{$jamroom_url}/{$item.trace_data.profile_url}/{$murl}/{$item.trace_item_id}/{$item.trace_data.audio_title_url}">{$item.trace_data.audio_title}</a><br>
                    {elseif $item.trace_module == 'jrVideo'}
                    {jrCore_module_url module="jrVideo" assign="murl"}
                    <a href="{$jamroom_url}/{$item.trace_data.profile_url}/{$murl}/{$item.trace_item_id}/{$item.trace_data.video_title_url}">{$item.trace_data.video_title}</a><br>
                    {elseif $item.trace_module == 'jrSoundCloud'}
                    {jrCore_module_url module="jrSoundCloud" assign="murl"}
                    <a href="{$jamroom_url}/{$item.trace_data.profile_url}/{$murl}/{$item.trace_item_id}/{$item.trace_data.soundcloud_title_url}">{$item.trace_data.soundcloud_title}</a><br>
                    {elseif $item.trace_module == 'jrVimeo'}
                    {jrCore_module_url module="jrVimeo" assign="murl"}
                    <a href="{$jamroom_url}/{$item.trace_data.profile_url}/{$murl}/{$item.trace_item_id}/{$item.trace_data.vimeo_title_url}">{$item.trace_data.vimeo_title}</a><br>
                    {elseif $item.trace_module == 'jrYouTube'}
                    {jrCore_module_url module="jrYouTube" assign="murl"}
                    <a href="{$jamroom_url}/{$item.trace_data.profile_url}/{$murl}/{$item.trace_item_id}/{$item.trace_data.youtube_title_url}">{$item.trace_data.youtube_title}</a><br>
                    {/if}
                    @&nbsp;<span class="hilite2">{$item._updated|date_format:"h:i"}</span>&nbsp;On&nbsp;<span class="hilite2">{$item._updated|date_format:"M d Y"}</span><br>
                </div>
            </div>
        </div>

    </div>

    {/foreach}
    <hr>
    {if $info.total_pages > 1}
    <div class="table-div" style="width:100%;">
        <div class="table-row-div">
            <div class="table-cell-div right p5 middle" style="width:10%;">
                {if isset($info.prev_page) && $info.prev_page > 0}
                <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrLoad('#fan_activity','{$info.page_base_url}/p={$info.prev_page}');">
                {else}
                <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button form_button_disabled">
                {/if}
            </div>
            <div class="table-cell-div center p5 middle" style="width:80%;">
                {if $info.total_pages <= 5}
                {$info.page} &nbsp;/ {$info.total_pages}
                {else}
                <form name="form" method="post" action="_self">
                    <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad=('#fan_activity','{$info.page_base_url}/p=' +sel);">
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
            </div>
            <div class="table-cell-div left p5 middle" style="width:10%;">
                {if isset($info.next_page) && $info.next_page > 1}
                <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrLoad('#fan_activity','{$info.page_base_url}/p={$info.next_page}');">
                {else}
                <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button form_button_disabled">
                {/if}
            </div>
        </div>
    </div>
    {/if}

    {else}
        No Activity Today in the past {$_conf.jrTrace_trace_history} days!
    {/if}
    {/literal}
{/capture}

{jrCore_list module="jrTrace" order_by="_created desc" search1="profile_quota_id in `$_conf.jrSoloArtist_fan_quota_id`" template=$fan_activity_row pagebreak="10" page=$_post.p}
