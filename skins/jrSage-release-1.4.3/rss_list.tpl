{if isset($jrFeed.feed)}
    <div class="title">{$jrFeed.feed.title}</div>
    <div class="body_2">
        <div class="body_3" style="height:350px;overflow:auto;">
            <div class="normal" style="text-align:center;">{$jrFeed.feed.description}</div>
            {if isset($jrFeed.feed.item)}
                <div class="block">
                    {foreach from=$jrFeed.feed.item item="item"}
                        <div class="normal">
                            <a href="{$item.link}">{$item.title}</a><br>
                            {$item.pubDate|jrCore_date_format}<br>
                            {$item.description}<br>
                        </div>
                        <hr>
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>
{/if}

