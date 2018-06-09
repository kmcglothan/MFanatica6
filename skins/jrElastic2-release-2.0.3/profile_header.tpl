{jrCore_include template="header.tpl"}
{jrCore_module_url module="jrProfile" assign="murl"}

<section id="profile">

    {* set up default crop *}
    {$crop = "10:1"}
    {if jrCore_is_mobile_device()}
        {$crop = "auto"}
    {/if}

    <div id="profile_header">
        <div class="clearfix">

            <div class="profile_header_image">
                {if $profile_header_image_size > 0}
                    <a href="{$jamroom_url}/{$murl}/image/profile_header_image/{$_profile_id}/1280" data-lightbox="profile_header" title="{jrCore_lang skin="jrElastic2" id=69 default="Click to see full image"}">
                        {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_header_image" item_id=$_profile_id size="1280" class="img_scale" crop=$crop alt=$profile_name _v=$profile_header_image_time}
                    </a>
                {/if}
            </div>

            <div class="profile_info">
                <div class="wrap">
                    <div class="row">
                        <div class="col6">
                            <div class="table">
                                <div class="table-row">
                                    <div class="table-cell profile-image">
                                        <div class="profile_image">
                                            {if jrProfile_is_profile_owner($_profile_id)}
                                                {jrCore_module_url module="jrProfile" assign="purl"}
                                                {jrCore_lang skin="jrElastic2" id=66 default="Edit" assign="hover"}
                                                <a href="{$_conf.jrCore_base_url}/{$purl}/settings/profile_id={$_profile_id}">
                                                    {jrCore_module_function
                                                    function="jrImage_display"
                                                    module="jrProfile"
                                                    type="profile_image"
                                                    item_id=$_profile_id
                                                    size="xlarge"
                                                    class="img_scale img_shadow"
                                                    alt=$profile_name
                                                    crop="auto"
                                                    title=$hover
                                                    width=false
                                                    height=false}</a>
                                                <div class="profile_hoverimage">
                                                    <span class="normal">{$hover}</span><br>
                                                    {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Edit" color="ffffff"}
                                                </div>
                                            {else}
                                                {jrCore_module_function
                                                function="jrImage_display"
                                                module="jrProfile"
                                                type="profile_image"
                                                item_id=$_profile_id
                                                size="xxlarge"
                                                crop="auto"
                                                class="img_scale img_shadow"
                                                alt=$profile_name
                                                width=false
                                                height=false}
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="table-cell">
                                        <div class="profile_name">
                                            {$profile_name|truncate:55}<br>
                                            <span><a href="{$jamroom_url}/{$profile_url}">@{$profile_url|rawurldecode}</a> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col6">
                           <div class="table profile_buttons">
                               <div class="table-row">
                                   <div class="table-cell" style="text-align: right">
                                       {jrCore_lang id=62 skin="jrElastic2" default="Follow" assign="Follow"}
                                       {jrFollower_button profile_id=$_profile_id title=$follow}
                                       {jrCore_lang skin="jrElastic2" id=66 default="Edit" assign="edit"}
                                       {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title=$edit}
                                       {if jrUser_is_admin() || jrUser_is_power_user()}
                                           {jrCore_lang skin="jrElastic2" id=2 default="Create" assign="create"}
                                           {jrCore_item_create_button module="jrProfile" view="create" profile_id=$_profile_id title=$create}
                                       {/if}
                                       {jrProfile_delete_button profile_id=$_profile_id}
                                   </div>
                               </div>
                           </div>
                        </div>
                    </div>





                </div>
            </div>
        </div>
    </div>

    <section id="profile_menu" style="overflow: visible">
        <div class="menu_banner clearfix">
            <div>
                {$menu_template = "profile_menu.tpl"}
                {if jrCore_is_mobile_device()}
                    {$menu_template = "profile_menu_mobile.tpl"}
                {/if}
                {jrProfile_menu template=$menu_template profile_quota_id=$profile_quota_id profile_url=$profile_url order="jrAction,jrBlog,jrCombinedAudio,jrAudio,jrCombinedVideo,jrVideo,jrGallery,jrGroup,jrEvent,jrYouTube,jrVimeo,jrFlickr"}
            </div>
        </div>
    </section>

    <div class="row profile_body">

    {if $profile_disable_sidebar != 1}
        {jrCore_include template="profile_sidebar.tpl"}
    {/if}

    {* next <div> starts in body *}
