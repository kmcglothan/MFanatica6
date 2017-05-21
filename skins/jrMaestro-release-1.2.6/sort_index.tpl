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
                   href="#"></a>
            {/if}
        </li>
    {elseif $nav_mode == 'jrVideo'}
        <li id="channels_tab"{$class}>
            {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedVideo" id=1 default="Video"}"
                   href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedVideo"}"></a>
            {else}
                <a title="{jrCore_lang module="jrVideo" id=35 default="Video"}"
                   href="#"></a>
            {/if}
        </li>
    {elseif $nav_mode == 'jrVimeo'}
        <li id="vimeo_tab"{$class}>
            {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedVideo" id=1 default="Video"}"
                   href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedVideo"}"></a>
            {else}
                <a title="{jrCore_lang module="jrVimeo" id=38 default="Vimeo"}"
                   href="#"></a>
            {/if}
        </li>
    {elseif $nav_mode == 'jrBlog'}
        <li id="blog_tab"{$class}>
            <a title="{jrCore_lang skin="jrMaestro" id=23 default="Blog"}"
               href="#"></a>
        </li>
    {elseif $nav_mode == 'jrAction'}
        <li id="home_tab">
            <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}"
               title="{jrCore_lang module="jrAction" id="4" default="Timeline"}"></a>
        </li>
        <li id="mention_tab">
            <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/mentions"
               title="{jrCore_lang module="jrAction" id="7" default="Mentions"}"></a>
        </li>

    {elseif $nav_mode == 'jrFollower'}
        <li id="followers_tab"{$class}>
            <a href="#"
               title="{jrCore_lang skin="jrMaestro" id="15" default="Followers"}"></a>
        </li>
    {elseif $nav_mode == 'jrEvent'}
        <li id="calendar_tab"{$class}><a href="#" title="{jrCore_lang module="jrEvent" id=31 default="Event"}"></a></li>
    {elseif $nav_mode == 'jrPage'}
        <li id="page_tab"{$class}><a href="#" title="{jrCore_lang module="jrEvent" id=31 default="Event"}"></a></li>
    {elseif $nav_mode == 'jrSoundCloud'}
        <li id="soundcloud_tab"{$class}><a href="#"
                                           title="{jrCore_lang module="jrSoundCloud" id="53" default="SoundCloud"}"></a>
        </li>
    {elseif $nav_mode == 'jrYouTube'}
        <li id="youtube_tab"{$class}><a href="#" title="{jrCore_lang module="jrYouTube" id="40" default="YouTube"}"></a>
        </li>
    {elseif $nav_mode == 'jrGallery'}
        <li id="gallery_tab"{$class}><a href="#"
                                        title="{jrCore_lang module="jrGallery" id=24 default="Image Galleries"}"></a>
        </li>
    {elseif $nav_mode == 'jrSoundCloud'}
        <li id="soundcloud_tab"{$class}><a href="#"
                                           title="{jrCore_lang module="jrSoundCloud" id="53" default="SoundCloud"}"></a>
        </li>
    {elseif $nav_mode == 'jrStore'}
        <li id="cart_tab"><a href="#" title="{jrCore_lang module="jrSoundCloud" id="53" default="SoundCloud"}"></a></li>
        <li id="stats_tab"><a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/sales"
                              title="{jrCore_lang module="jrSoundCloud" id="53" default="SoundCloud"}"></a></li>
        <li id="settings_tab"><a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/settings"
                                 title="{jrCore_lang module="jrSoundCloud" id="53" default="SoundCloud"}"></a></li>
    {elseif $nav_mode == 'jrProfile'}
        <li id="profile_tab"{$class}><a href="#" title="{jrCore_lang module="jrEvent" id=31 default="Event"}"></a></li>
    {elseif $nav_mode == 'jrFAQ'}
        <li id="faq_tab"{$class}><a href="#" title="{jrCore_lang module="jrFAQ" id="10" default="FAQ"}"></a></li>
    {elseif $nav_mode == 'jrDocs'}
        <li id="category_tab"{$class}><a href="#" title="{jrCore_lang module="jrDocs" id="53" default="Documentation"}"></a></li>
    {elseif $nav_mode == 'jrFlickr'}
        <li id="gallery_tab"{$class}><a href="#" title="{jrCore_lang module="jrFlickr" id="1" default="Flickr"}"></a></li>
    {elseif $nav_mode == 'jrPlaylist'}
        <li id="album_tab"{$class}><a href="#" title="{jrCore_lang module="jrFlickr" id="1" default="Flickr"}"></a></li>
    {elseif $nav_mode == 'jrTags'}
        <li id="tags_tab"{$class}><a href="#" title="{jrCore_lang module="jrTags" id="2" default="Tag"}"></a></li>
    {elseif $nav_mode == 'jrGuestBook'}
        <li id="edit_tab"{$class}><a href="#" title="{jrCore_lang module="jrGuestBook" id="20" default="GuestBook"}"></a></li>
    {elseif $nav_mode == 'jrForum'}
        <li id="forum_tab"{$class}><a href="#" title="{jrCore_lang module="jrForum" id="20" default="Forum"}"></a></li>
    {elseif $nav_mode == 'jrSearch'}
        <li id="search_tab"{$class}><a href="#" title="{jrCore_lang module="jrSearch" id="7" default="Search"}"></a></li>
    {elseif $nav_mode == 'jrFoxyCartBundle'}
        <li id="cart_tab"{$class}><a href="#" title="{jrCore_lang module="jrFoxyCartBundle" id="1" default="Item Bundles"}"></a></li>
    {elseif $nav_mode == 'jrPoll'}
        <li id="stats_tab"{$class}><a href="#" title="{jrCore_lang module="jrPoll" id="1" default="Poll"}"></a></li>
    {elseif $nav_mode == 'jrPhotoAlbum'}
        <li id="gallery_tab"{$class}><a href="#" title="{jrCore_lang module="jrPhotoAlbum" id="11" default="photo album"}"></a></li>
    {elseif $nav_mode == 'jrGroup'}
        <li id="group_tab"{$class}><a href="#" title="{jrCore_lang module="jrGroup" id="1" default="Groups"}"></a></li>
    {elseif $nav_mode == 'jrGroupDiscuss'}
        <li id="forum_tab"{$class}><a href="#" title="{jrCore_lang module="jrGroupDiscuss" id="1" default="Discussions"}"></a></li>
    {elseif $nav_mode == 'jrGroupPage'}
        <li id="page_tab"{$class}><a href="#" title="{jrCore_lang module="jrGroupPage" id="1" default="Group Page"}"></a></li>
    {elseif $nav_mode == 'jrGroupDiscuss'}
        <li id="page_tab"{$class}><a href="#" title="{jrCore_lang module="jrGroupDiscuss" id="1" default="Discussions"}"></a></li>
    {else}
        <li id="star_tab"{$class}><a href="#" title="{$title}"></a></li>
    {/if}


    {if $_conf.n8ISkin_gridlist == 'on'}
        <li id="list_tab" class="active"><a href="#" title="List Display"></a></li>
        <li id="grid_tab"><a href="#" title="Grid Display"></a></li>
    {/if}
    {if jrCore_module_is_active('n8Ajax') && $_conf.n8ISkin_sortable == 'on'}
        <li id="new_tab"{$newest}><a href="#" title="{jrCore_lang id=4 skin="jrMaestro" default="By Newest"}"></a></li>
        <li id="alphabet_tab"{$alphabet}><a href="#"
                                            title="{jrCore_lang id=5 skin="jrMaestro" default="Alphabetically"}"></a>
        </li>
        <li id="plays_tab"{$plays}><a href="#" title="{jrCore_lang id=6 skin="jrMaestro" default="By Most Plays"}"></a>
        </li>
        <li id="like_tab"{$likes}><a href="#" title="{jrCore_lang id=7 skin="jrMaestro" default="By Most Likes"}"></a>
        </li>
    {/if}
</ul>