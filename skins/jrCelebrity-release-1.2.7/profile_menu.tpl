{if isset($_items)}
    <ul id="horizontal">

        {if isset($_post.option) && strlen($_post.option) > 0}
            <li><a href="{$jamroom_url}/{$profile_url}">{jrCore_lang skin="jrCelebrity" id=1 default="Home"}</a></li>
        {else}
            <li class="active"><a href="{$jamroom_url}/{$profile_url}">{jrCore_lang skin="jrCelebrity" id=1 default="Home"}</a></li>
        {/if}

        {foreach $_items as $module => $entry}
            {if $entry.active == '1'}
                <li class="active t{$module}"><a href="{$entry.target}">{$entry.label}</a></li>
            {else}
                <li class="t{$module}"><a href="{$entry.target}">{$entry.label}</a></li>
            {/if}
        {/foreach}

    </ul>
{/if}

