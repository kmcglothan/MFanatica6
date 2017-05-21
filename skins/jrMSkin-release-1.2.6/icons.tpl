{jrCore_module_url module=$nav_mode assign="nav_murl"}

{if !jrCore_module_is_active('n8Ajax')}
    {$class = ' class="solo"'}
{/if}
<ul id="actions_tab">
    {if $nav_mode == 'jrAudio'}
        <li class="solo">
            {if jrCore_module_is_active('jrCombinedAudio') && $item.quota_jrCombinedAudio_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}"
                   href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedAudio"}">
                    {jrCore_icon icon="audio" size="20" color="444444"}
                </a>
            {else}
                <a title="{jrCore_lang module="jrAudio" id=41 default="Audio"}"
                   href="#">
                    {jrCore_icon icon="audio" size="20" color="444444"}
                </a>
            {/if}
        </li>
    {elseif $nav_mode == 'jrVideo'}
        <li class="solo">
            {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedVideo" id=1 default="Video"}"
                   href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedVideo"}">
                    {jrCore_icon icon="video" size="20" color="444444"}
                </a>
            {else}
                <a title="{jrCore_lang module="jrVideo" id=35 default="Video"}"
                   href="#">
                    {jrCore_icon icon="video" size="20" color="444444"}
                </a>
            {/if}
        </li>
    {elseif $nav_mode == 'jrVimeo'}
        <li class="solo">
            {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
                <a title="{jrCore_lang module="jrCombinedVideo" id=1 default="Video"}"
                   href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedVideo"}">{jrCore_icon icon="video" size="20" color="444444"}</a>
            {else}
                <a title="{jrCore_lang module="jrVimeo" id=38 default="Vimeo"}"
                   href="#">{jrCore_icon icon="video" size="20" color="444444"}</a>
            {/if}
        </li>
    {elseif $nav_mode == 'jrBlog'}
        <li class="solo">
            <a title="{jrCore_lang skin="jrPost" id=23 default="Blog"}"
               href="#">{jrCore_icon icon="blog" size="20" color="444444"}</a>
        </li>
    {elseif $nav_mode == 'jrAction'}
        {if $single == true}
            <li class="solo">
                <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}"
                   title="{jrCore_lang module="jrAction" id="4" default="Timeline"}">
                    {jrCore_icon icon="home" size="20" color="444444"}
                </a>
            </li>
        {else}
            <li>
                <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}"
                   title="{jrCore_lang module="jrAction" id="4" default="Timeline"}">
                    {jrCore_icon icon="home" size="20" color="444444"}
                </a>
            </li>
            <li>
                <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/mentions"
                   title="{jrCore_lang module="jrAction" id="7" default="Mentions"}">
                    {jrCore_icon icon="mention" size="20" color="444444"}
                </a>
            </li>
        {/if}

    {elseif $nav_mode == 'jrFollower'}
        <li class="solo">
            <a href="#"
               title="{jrCore_lang skin="jrPost" id="15" default="Followers"}">
                {jrCore_icon icon="followers" size="20" color="444444"}
            </a>
        </li>
    {elseif $nav_mode == 'jrEvent'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrEvent" id=31 default="Event"}">
                {jrCore_icon icon="calendar" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrPage'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrEvent" id=31 default="Event"}">
                {jrCore_icon icon="page" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrSoundCloud'}
        <li class="solo"><a href="#"
                                           title="{jrCore_lang module="jrSoundCloud" id="53" default="SoundCloud"}">
                {jrCore_icon icon="audio" size="20" color="444444"}
            </a>
        </li>
    {elseif $nav_mode == 'jrYouTube'}
        <li id="youtube_tab" class="solo"><a href="#" title="{jrCore_lang module="jrYouTube" id="40" default="YouTube"}">
                {jrCore_icon icon="video" size="20" color="444444"}
            </a>
        </li>
    {elseif $nav_mode == 'jrGallery'}
        <li class="solo"><a href="#"
                                        title="{jrCore_lang module="jrGallery" id=24 default="Image Galleries"}">
                {jrCore_icon icon="gallery" size="20" color="444444"}
            </a>
        </li>
    {elseif $nav_mode == 'jrStore'}

       {if $single == true}
           <li class="solo"><a href="#" title="{jrCore_lang module="jrStore" id="19" default="Products"}">
                   {jrCore_icon icon="store" size="20" color="444444"}
               </a></li>
       {else}
           <li><a href="#" title="{jrCore_lang module="jrStore" id="19" default="Products"}">
                   {jrCore_icon icon="store" size="20" color="444444"}
               </a></li>
           <li><a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/sales"
                  title="{jrCore_lang module="jrStore" id="34" default="Product Sales"}">
                   {jrCore_icon icon="stats" size="20" color="444444"}
               </a></li>
           <li><a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/settings"
                  title="{jrCore_lang module="jrStore" id="35" default="Settings"}">
                   {jrCore_icon icon="control" size="20" color="444444"}
               </a></li>
       {/if}


    {elseif $nav_mode == 'jrProfile'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrProfile" id="26" default="Profiles"}">
                {jrCore_icon icon="profile" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrFAQ'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrFAQ" id="10" default="FAQ"}">
                {jrCore_icon icon="faq" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrDocs'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrDocs" id="53" default="Documentation"}">
                {jrCore_icon icon="page" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrFlickr'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrFlickr" id="1" default="Flickr"}">
                {jrCore_icon icon="gallery" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrPlaylist'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrFlickr" id="1" default="Flickr"}">
                {jrCore_icon icon="audio" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrTags'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrTags" id="2" default="Tag"}">
                {jrCore_icon icon="tag" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrGuestBook'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrGuestBook" id="20" default="GuestBook"}">
                {jrCore_icon icon="guestbook" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrForum'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrForum" id="20" default="Forum"}">
                {jrCore_icon icon="forum" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrSearch'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrSearch" id="7" default="Search"}">
                {jrCore_icon icon="search" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrFoxyCartBundle'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrFoxyCartBundle" id="1" default="Item Bundles"}">
                {jrCore_icon icon="store" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrPoll'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrPoll" id="1" default="Poll"}">
                {jrCore_icon icon="stats" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrPhotoAlbum'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrPhotoAlbum" id="11" default="photo album"}">
                {jrCore_icon icon="gallery" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrGroup'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrGroup" id="1" default="Groups"}">
                {jrCore_icon icon="group" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrGroupDiscuss'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrGroupDiscuss" id="1" default="Discussions"}">
                {jrCore_icon icon="forum" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrGroupPage'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrGroupPage" id="1" default="Group Page"}">
                {jrCore_icon icon="page" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'jrGroupDiscuss'}
        <li class="solo"><a href="#" title="{jrCore_lang module="jrGroupDiscuss" id="1" default="Discussions"}">
                {jrCore_icon icon="forum" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'about'}
        <li class="solo"><a href="#" title="{jrCore_lang skin="jrMSkin" id="12" default="About"}">
                {jrCore_icon icon="info" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'contact'}
        <li class="solo"><a href="#" title="{jrCore_lang skin="jrMSkin" id="12" default="About"}">
                {jrCore_icon icon="mail" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'bio'}
        <li class="solo"><a href="#" title="{jrCore_lang skin="jrMSkin" id="12" default="About"}">
                {jrCore_icon icon="bio" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'online'}
        <li class="solo"><a href="#" title="{jrCore_lang skin="jrMSkin" id="11" default="Online Status"}">
                {jrCore_icon icon="online" size="20" color="444444"}
            </a></li>
     {elseif $nav_mode == 'stats'}
        <li class="solo"><a href="#" title="{jrCore_lang skin="jrMSkin" id="13" default="Stats"}">
                {jrCore_icon icon="stats" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'menu'}
        <li class="solo"><a href="#" title="{jrCore_lang skin="jrMSkin" id="13" default="Stats"}">
                {jrCore_icon icon="menu" size="20" color="444444"}
            </a></li>
    {elseif $nav_mode == 'trending'}
        <li class="solo"><a href="#" title="{jrCore_lang skin="jrMSkin" id="146" default="Trending"}">
                {jrCore_icon icon="trending" size="20" color="444444"}
            </a></li>
    {else}
        <li class="solo"><a href="#" title="{$title}">
                {jrCore_icon icon="star" size="20" color="444444"}
            </a></li>
    {/if}

</ul>