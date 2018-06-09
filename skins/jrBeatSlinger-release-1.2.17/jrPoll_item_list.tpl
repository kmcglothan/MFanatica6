{jrCore_module_url module="jrPoll" assign="murl"}
{if isset($_items)}

    <script type="text/javascript">
        $(document).ready(function() {
            $('span.countdown').each(function(i,e) {
                var n = Number($(this).text());
                var c = new Date(n);
                if (typeof c != "undefined") {
                    $(this).countdown( { until:c, format: 'dHM' } );
                }
            } );
        } );
    </script>

    {foreach from=$_items item="item"}

    <div class="item" style="position:relative">

        <div class="block_config">
            {jrCore_item_list_buttons module="jrPoll" item=$item}
        </div>

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

    </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
