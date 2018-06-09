{jrCore_module_url module=$nav_mode assign="nav_murl"}
<ul id="actions_tab">
    {if $nav_mode == 'jrAudio'}
        <li id="home_tab">
            {if jrCore_module_is_active('jrCombinedAudio') && $item.quota_jrCombinedAudio_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}" href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedAudio"}"></a>
            {else}
                <a title="{jrCore_lang module="jrAudio" id=41 default="Audio"}" href="{$jamroom_url}/{$item.profile_url}/{$nav_murl}"></a>
            {/if}
        </li>
        <li id="albums_tab"><a href="{$jamroom_url}/{$item.profile_url}/{$nav_murl}/albums" title="Albums"></a></li>
        <li id="album_tab"><a href="{$jamroom_url}/{$item.profile_url}/{$nav_murl}/albums/{$item.audio_album_url}" title="{$item.audio_album}"></a></li>
    {elseif $nav_mode == "jrVideo"}
        <li id="home_tab">
            {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedVideo" id=1 default="Video"}" href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedVideo"}"></a>
            {else}
                <a title="{jrCore_lang module="jrVideo" id=35 default="Video"}" href="{$jamroom_url}/{$item.profile_url}/{$nav_murl}"></a>
            {/if}
        </li>
        <li id="albums_tab"><a href="{$jamroom_url}/{$item.profile_url}/{$nav_murl}/albums" title="Albums"></a></li>
        <li id="album_tab"><a href="{$jamroom_url}/{$item.profile_url}/{$nav_murl}/albums/{$item.video_album_url}" title="{$item.video_album}"></a></li>
    {elseif $nav_mode == "jrBlog"}
        <ul id="actions_tab">
            <li id="home_tab"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}" title="{jrCore_lang module="jrBlog" id="24" default="Blog"}"></a></li>
            <li id="categories_tab"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category" title="{jrCore_lang skin="jrMSkin" id="23" default="Blog"}"></a></li>
            <li id="category_tab"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url}" title="{$item.blog_category}"></a></li>
        </ul>
    {elseif $nav_mode == "jrVimeo"}
        <ul id="actions_tab">
            <li id="home_tab" class="solo"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}" title="{jrCore_lang module="jrVimeo" id="38" default="Vimeo"}"></a></li>
        </ul>
    {/if}
</ul>