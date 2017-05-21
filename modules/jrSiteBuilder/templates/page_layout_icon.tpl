<div class="sb_page_icon">
    {foreach $layout as $_row}
    <div class="row">
        <div class="sb_page_icon-row">
            {foreach $_row as $_col}
            <div class="col{$_col.span} center">
                <div class="sb_page_icon-cell">{$_col.span}</div>
            </div>
            {/foreach}
        </div>
    </div>
    {/foreach}
</div>
