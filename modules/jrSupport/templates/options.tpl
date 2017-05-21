<div class="item">

    {if isset($market_title)}
    <div id="jrsupport-options" style="display:table;width:100%">

        <div style="display:table-row">
            <div class="p10" style="display:table-cell;width:100%">
                <h2>{$market_title}</h2><br>by <a href="{$profile_url}" target="_blank">@{$profile_url|basename}</a>
            </div>
        </div>

        <div style="display:table-row">
            <div class="jrsupport-entry">
                <a href="{$documentation_url}" target="_blank"><input type="button" value="Documentation" class="form_button form_button_support"></a> &nbsp; View the online documentation for this item
            </div>
        </div>

        <div style="display:table-row">
            <div class="jrsupport-entry">
                <a href="{$forum_url}" target="_blank"><input type="button" value="Community Forum" class="form_button form_button_support"></a> &nbsp; Check for Help and Answers in the Community Forum
            </div>
        </div>

        {if isset($priority_url)}
        <div style="display:table-row">
            <div class="jrsupport-entry">
                <a href="{$priority_url}" target="_blank"><input type="button" value="Support Ticket" class="form_button form_button_support"></a> &nbsp; Open a Support Ticket to get help with your questions
            </div>
        </div>
        {/if}

        {if isset($market_url)}
        <div style="display:table-row">
            <div class="jrsupport-entry">
                <a href="{$market_url}" target="_blank"><input type="button" value="Product Detail" class="form_button form_button_support"></a> &nbsp; View information about this item including detailed Change Log
            </div>
        </div>
        {/if}

    </div>

    {else}

    <div class="center">
        <div class="p10 error rounded">
        {if isset($error)}
            {$error}
        {else}
            No support information available for the selected item
        {/if}
        </div>
    </div>

    {/if}

</div>
