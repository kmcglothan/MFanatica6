{jrCore_module_url module="jrPoll" assign="murl"}

{if isset($_items)}
    {foreach $_items as $item}

        {jrMaestro_process_item item=$item module="jrPoll" assign="_item"}
        <div class="list_item">
           <div class="wrap">
               <div class="poll_status" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.poll_title_url}');">
                   {if $smarty.now >= $item.poll_start_date && $smarty.now < $item.poll_end_date}
                       <span class="poll_open">{jrCore_lang module="jrPoll" id="47" default="Open for Voting"}</span>
                   {elseif $smarty.now < $item.poll_start_date}
                       <div class="poll_pending">
                           {jrCore_lang module="jrPoll" id="48" default="Voting begins"}:<br>
                           <span class="countdown">{$item.poll_start_date}000</span>
                           <div style="clear:both"></div>
                       </div>
                   {else}
                       <span class="poll_closed">{jrCore_lang module="jrPoll" id="49" default="Voting has Ended"}</span>
                   {/if}
               </div>

               <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.poll_title_url}">{$item.poll_title}</a></h2>

               <div class="mt20">
                   {$item.poll_description|jrCore_format_string:$item.profile_quota_id}
               </div>

               <div class="data clearfix">
                   <span>{$item.poll_comment_count|jrCore_number_format} {jrCore_lang skin="jrMaestro" id="109" default="Comments"}</span>
                   <span>{$item.poll_like_count|jrCore_number_format} {jrCore_lang skin="jrMaestro" id="110" default="Likes"}</span>
               </div>
           </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}