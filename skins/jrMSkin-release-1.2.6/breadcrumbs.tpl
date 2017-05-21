{jrCore_module_url module=$module assign="nav_murl"}

{if $module == "jrAction"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrAction" id="4" default="Timeline"}</a>

    {if $page == 'mentions'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/mentions">{jrCore_lang module="jrAction" id="7" default="Mentions"}</a>
    {/if}

    {if $page == 'feedback'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/feedback">{jrCore_lang skin="jrMSkin" id=120 default="Feedback"}</a>
    {/if}

    {if $page == 'detail'}
        {if $page == 'detail'}
            <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}">{jrCore_lang module="jrAction" id="27" default="Activity Update"}</a>
        {/if}
    {/if}
{elseif $module == "jrAudio"}
    {if jrCore_module_is_active('jrCombinedAudio') && $item.quota_jrCombinedAudio_allowed == 'on'}
        <a href="{$jamroom_url}/{$profile_url}/{jrCore_module_url module="jrCombinedAudio"}">{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</a>
    {else}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrAudio" id="41" default="Audio"}</a>
    {/if}
    {if $page == 'detail' || $page == 'group'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/albums">{jrCore_lang module="jrAudio" id="34" default="Albums"}</a>
    {/if}
    {if strlen($item.audio_album) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a>
    {/if}
    {if strlen($item.audio_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a>
    {/if}
{elseif $module == "jrCombinedAudio"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</a>
    {if $page == 'detail' || $page == 'group'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/albums">{jrCore_lang module="jrAudio" id="34" default="Albums"}</a>
    {/if}
    {if strlen($item.audio_album) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a>
    {/if}
    {if strlen($item.audio_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a>
    {/if}
{elseif $module == "jrCombinedVideo"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrCombinedVideo" id=1 default="Audio"}</a>
    {if $page == 'detail' || $page == 'group'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/albums">{jrCore_lang module="jrAudio" id="34" default="Albums"}</a>
    {/if}
    {if strlen($item.video_album) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/albums/{$item.video_album_url}">{$item.video_album}</a>
    {/if}
    {if strlen($item.video_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.video_title_url}">{$item.video_title}</a>
    {/if}
{elseif $module == "jrVideo"}
    {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
        <a href="{$jamroom_url}/{$profile_url}/{jrCore_module_url module="jrCombinedVideo"}">{jrCore_lang module="jrCombinedVideo" id=1 default="Video"}</a>
    {else}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrVideo" id="35" default="Video"}</a>
    {/if}
    {if $page == 'detail' || $page == 'group'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/albums">{jrCore_lang module="jrVideo" id="34" default="Albums"}</a>
    {/if}
    {if strlen($item.video_album) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/albums/{$item.video_album_url}">{$item.video_album}</a>
    {/if}
    {if strlen($item.video_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.video_title_url}">{$item.video_title}</a>
    {/if}
{elseif $module == "jrBlog"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrBlog" id="24" default="Blog"}</a>
    {if $page == 'detail' || $page == 'group'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/category">{jrCore_lang skin="jrMSkin" id="22" default="Categories"}</a>
    {/if}
    {if strlen($item.blog_category) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/category/{$item.blog_category_url}">{$item.blog_category}</a>
    {/if}
    {if strlen($item.blog_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a>
    {/if}
{elseif $module == "jrEvent"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrEvent" id="31" default="Event"}</a>
    {if $page == 'detail' || $page == 'group'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/calendar">{jrCore_lang skin="jrMSkin" id="30" default="Calendar"}</a>
    {/if}
    {if strlen($item.event_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a>
    {/if}
{elseif $module == "jrVimeo"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrVimeo" id="38" default="Vimeo"}</a>
    {if strlen($item.vimeo_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.vimeo_title_url}">{$item.vimeo_title|truncate:85}</a>
    {/if}
{elseif $module == "jrFoxyCartBundle"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrFoxyCartBundle" id="1" default="Item Bundles"}</a>
    {if strlen($item.bundle_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.bundle_title_url}">{$item.bundle_title}</a>
    {/if}
{elseif $module == "jrPage"}
    {if $item.page_location == 1}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrPage" id="19" default="Pages"}</a>
        {if strlen($item.page_title) > 0 && $page == 'detail'}
            <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.page_title_url}">{$item.page_title}</a>
        {/if}
    {else}
        <a href="{$jamroom_url}/{$nav_murl}">{jrCore_lang module="jrPage" id="19" default="Pages"}</a>
        {if strlen($item.page_title) > 0 && $page == 'detail'}
            <a href="{$jamroom_url}/{$nav_murl}/{$item._item_id}/{$item.page_title_url}">{$item.page_title}</a>
        {/if}
    {/if}
{elseif $module == "jrFAQ"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrFAQ" id="10" default="FAQ"}</a>
{elseif $module == "jrTags"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrTags" id="2" default="Tag"}</a>
{elseif $module == "jrFlickr"}

    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrFlickr" id="1" default="Flickr"}</a>
    {if strlen($item.flickr_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.flickr_title_url}">{$item.flickr_title}</a>
    {/if}
{elseif $module == "jrSoundCloud"}
    {if jrCore_module_is_active('jrCombinedAudio') && $item.quota_jrCombinedAudio_allowed == 'on'}
        <a href="{$jamroom_url}/{$profile_url}/{jrCore_module_url module="jrCombinedAudio"}">{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</a>
    {else}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrSoundCloud" id="53" default="SoundCloud"}</a>
    {/if}
    {if strlen($item.soundcloud_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.soundcloud_title_url}">{$item.soundcloud_title|truncate:85}</a>
    {/if}
{elseif $module == "jrYouTube"}
    {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
        <a href="{$jamroom_url}/{$profile_url}/{jrCore_module_url module="jrCombinedVideo"}">{jrCore_lang module="jrCombinedVideo" id=1 default="Videos"}</a>
    {else}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrYouTube" id="40" default="YouTube"}</a>
    {/if}
    {if strlen($item.youtube_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.youtube_title_url}">{$item.youtube_title|truncate:85}</a>
    {/if}
{elseif $module == "jrGallery"}
    {if isset($quota_jrGallery_gallery_group) && $quota_jrGallery_gallery_group == 'off'}
        {jrCore_lang module="jrGallery" id=38 default="Images" assign="heading"}
    {else}
        {jrCore_lang module="jrGallery" id=24 default="Image Galleries" assign="heading"}
    {/if}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{$heading}</a>

    {if strlen($item.gallery_title) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item.gallery_title_url}/all">{$item.gallery_title}</a>
    {/if}

    {if strlen($item.gallery_image_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.gallery_image_title_url}">{$item.gallery_image_title}</a>
    {/if}
{elseif $module == 'jrStore'}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrStore" id="19" default="Products"}</a>

    {if $page == 'detail' || $page == 'group'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/category">{jrCore_lang module="jrStore" id="21" default="Category"}</a>
    {/if}

    {if strlen($item.product_category) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/category/{$item.product_category_url}">{$item.product_category}</a>
    {/if}
    {if strlen($item.product_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.product_title_url}">{$item.product_title}</a>
    {/if}
{elseif $module == 'jrDocs'}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrDocs" id="53" default="Documentation"}</a>
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/contents">{jrCore_lang module="jrDocs" id="54" default="Table of Contents"}</a>
    {if strlen($item.category) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$category_url}">{$category}</a>
    {/if}
    {if strlen($breadcrumb_url) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}{$breadcrumb_url}">{jrCore_lang module="jrDocs" id="63" default="Search Results"}</a>
    {/if}
{elseif $module == 'jrPlaylist'}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrPlaylist" id="9" default="Playlist"}</a>
    {if strlen($item.playlist_title) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.playlist_title_url}">{$item.playlist_title}</a>
    {/if}
{elseif $module == 'jrForum'}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrForum" id=36 default="Forum"}</a>
{elseif $module == 'jrDoc'}
   <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrDocs" id="53" default="Documentation"}</a>
{elseif $module == "jrFollower"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrFollower" id="26" default="followers"}</a>
{elseif $module == "jrProfile"}
    <a href="{$jamroom_url}/{$profile_url}">{$title}</a>
{elseif $module == "jrGuestBook"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrGuestBook" id="20" default="GuestBook"}</a>
{elseif $module == "jrPoll"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrPoll" id="1" default="Poll"}</a>
    {if strlen($item.poll_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.poll_title_url}">{$item.poll_title}</a>
    {/if}
{elseif $module == "jrPhotoAlbum"}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrPhotoAlbum" id="11" default="photo album"}</a>

    {if strlen($item.photoalbum_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.product_title_url}">{$item.photoalbum_title}</a>
    {/if}

{elseif $module == 'jrGroup'}
    <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}">{jrCore_lang module="jrGroup" id="1" default="Groups"}</a>
    {if strlen($item.group_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.product_title_url}">{$item.group_title}</a>
    {/if}

    {if strlen($item.group_category) > 0}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/category/{$item.product_category_url}">{$item.group_category}</a>
    {/if}
{elseif $module == 'jrGroupDiscuss'}

    {jrCore_module_url module="jrGroup" assign="gurl"}
    <a href="{$jamroom_url}/{$profile_url}/{$gurl}">{jrCore_lang module="jrGroup" id="1" default="Groups"}</a>
    {if isset($item.discuss_group_id)}
        <a href="{$jamroom_url}/{$profile_url}/{$gurl}/{$item.discuss_group_id}/{$item.group_title_url}">{$item.group_title}</a>
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/discussions/{$item.discuss_group_id}/{$item.group_title_url}">{jrCore_lang module="jrGroupDiscuss" id="1" default="Discussions"}</a>
    {else}
        <a href="{$jamroom_url}/{$profile_url}/{$gurl}/{$item._item_id}/{$item.group_title_url}">{$item.group_title}</a>
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/discussions/{$item._item_id}/{$item.group_title_url}">{jrCore_lang module="jrGroupDiscuss" id="1" default="Discussions"}</a>
    {/if}

    {if strlen($item.discuss_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.discuss_title_url}">{$item.discuss_title}</a>
    {/if}

{elseif $module == 'jrGroupPage'}
    {jrCore_module_url module="jrGroup" assign="gurl"}
    <a href="{$jamroom_url}/{$profile_url}/{$gurl}">{jrCore_lang module="jrGroup" id="1" default="Groups"}</a>
    {if isset($item.npage_group_id)}
        <a href="{$jamroom_url}/{$profile_url}/{$gurl}/{$item.npage_group_id}/{$item.group_title_url}">{$item.group_title}</a>
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/pages/{$item.npage_group_id}/{$item.group_title_url}">{jrCore_lang module="jrGroupPage" id=1 default="Group Pages"}</a>
    {else}
        <a href="{$jamroom_url}/{$profile_url}/{$gurl}/{$item._item_id}/{$item.group_title_url}">{$item.group_title}</a>
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/pages/{$item._item_id}/{$item.group_title_url}">{jrCore_lang module="jrGroupPage" id=1 default="Group Pages"}</a>
    {/if}

    {if strlen($item.npage_title) > 0 && $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/{$item.npage_title_url}">{$item.npage_title}</a>
    {/if}
{elseif $module == "jrComment"}
    <a href="{$jamroom_url}/{$item.profile_url}/{$nav_murl}">{jrCore_lang module="jrComment" id="11" default="Comments"}</a>
    {if $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/">{jrCore_lang skin="jrMSkin" id="147" default="Detail"}</a>
    {/if}
{elseif $module == "jrFile"}
    <a href="{$jamroom_url}/{$item.profile_url}/{$nav_murl}">{jrCore_lang module="jrFile" id="22" default="Files"}</a>
    {if $page == 'detail'}
        <a href="{$jamroom_url}/{$profile_url}/{$nav_murl}/{$item._item_id}/">{jrCore_lang skin="jrMSkin" id="147" default="Detail"}</a>
    {/if}
{/if}