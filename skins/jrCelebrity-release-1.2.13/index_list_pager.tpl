{* prev/next page profile footer links *}
{if $info.total_items > 0 && ($info.prev_page > 0 || $info.next_page > 0)}
    <a class="prev desk" href="#"></a>
    <ul class="pager desk">
        {if $info.total_pages > 1 && (!isset($pager_show_jumper) || $pager_show_jumper == '1')}
            {for $pages=1; $pages <= $info.total_pages; $pages++}
                {if $info.this_page == $pages}
                    <li><a class="page active" id="p{$pages}" href="#">{$pages}</a></li>
                {else}
                    <li><a class="page" id="p{$pages}" href="#">{$pages}</a></li>
                {/if}
            {/for}
        {/if}
    </ul>
    <a class="next desk" href="#"></a>
{/if}


