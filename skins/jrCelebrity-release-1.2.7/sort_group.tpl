{jrCore_module_url module=$nav_mode assign="nav_murl"}
{if !jrCore_module_is_active('n8Ajax')}
    {$class = ' class="solo"'}
{/if}

<ul id="actions_tab">
    {if $nav_mode == 'jrAudio'}

        <li id="album_tab"{$class}>
            {if jrCore_module_is_active('jrCombinedAudio') && $item.quota_jrCombinedAudio_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}"
                   href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedAudio"}"></a>
            {else}
                <a title="{jrCore_lang module="jrAudio" id=41 default="Audio"}"
                   href="{$jamroom_url}/{$profile_url}/{$nav_murl}"></a>
            {/if}
        </li>
    {elseif $nav_mode == 'jrVideo'}

        <li id="channels_tab"{$class}>
            {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedVideo" id=1 default="Video"}"
                   href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedVideo"}"></a>
            {else}
                <a title="{jrCore_lang module="jrVideo" id=35 default="Video"}"
                   href="{$jamroom_url}/{$profile_url}/{$nav_murl}"></a>
            {/if}
        </li>

    {elseif $nav_mode == "jrBlog"}

        <li id="blog_tab"{$class}>
            <a title="{jrCore_lang skin="jrCelebrity" id=23 default="Blog"}"
               href="{$jamroom_url}/{$profile_url}/{$nav_murl}"></a>
        </li>

    {elseif $nav_mode == "jrStore"}

        <li id="cart_tab"{$class}>
            <a title="{jrCore_lang module="jrStore" id="19" default="Products"}"
               href="{$jamroom_url}/{$profile_url}/{$nav_murl}"></a>
        </li>

    {/if}



    {if jrCore_module_is_active('n8Ajax')}
        <li id="new_tab"{$newest}><a href="#" title="By Newest"></a></li>
        <li id="alphabet_tab"{$alphabet}><a href="#" title="Alphabetically"></a></li>
    {/if}
</ul>