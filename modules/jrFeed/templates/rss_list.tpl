{if isset($jrFeed.feed)}
    <div class="title"><h2>{$jrFeed.feed.title}</h2></div>
    <div class="block_content">
        <div class="item" style="height:462px;overflow:auto;">
            {if isset($jrFeed.feed.item)}
                {foreach from=$jrFeed.feed.item item="item"}
                    <div class="normal">
                        <a href="{$item.link}">{$item.title}</a><br>
                        {$item.pubDate|jrCore_date_format}<br>
                        {$item.description}<br>
                    </div>
                    <hr>
                {/foreach}
            {/if}
        </div>
    </div>
{/if}

