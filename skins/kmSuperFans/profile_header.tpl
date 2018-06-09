{jrCore_include template="header.tpl"}
{jrCore_module_url module="jrProfile" assign="murl"}

<section id="profile">

    {* set up default crop *}
    {$crop = "4:1"}
    {if jrCore_is_mobile_device()}
        {$crop = "3:2"}
    {/if}

    <div id="profile_header">
        <div class="clearfix" style="position: relative;">
            {if $profile_header_image_size > 0}
                <a href="{$jamroom_url}/{$murl}/image/profile_header_image/{$_profile_id}/1280" data-lightbox="profile_header" title="{jrCore_lang skin="kmSuperFans" id=8 default="Click to see full image"}">
                    {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_header_image" item_id=$_profile_id size="1280" class="img_scale" crop=$crop alt=$profile_name _v=$profile_header_image_time}
                </a>
            {else}
                {if jrCore_is_mobile_device()}
                    {jrCore_image image="profile_header_image.jpg" width="800" class="img_scale" height="auto"}
                {else}
                    {jrCore_image image="profile_header_image_large.jpg" width="1280" class="img_scale" height="auto"}
                {/if}
            {/if}
            <div class="profile_hover"></div>
            {if jrProfile_is_profile_owner($_profile_id)}
                <div class="profile_admin_buttons">
                    <div class="row">
                        <div class="col6">
                            <div class="wrap">
                                <a class="camera" href="{$_conf.jrCore_base_url}/{$murl}/settings/profile_id={$_profile_id}">
                                    {jrCore_icon icon="camera2" size="32" color="ffffff"}
                                    {jrCore_lang skin="kmSuperFans" id=67 default="Update Cover Image"}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
            <div class="profile_info">
                <div class="wrap">
                    <div class="table">
                        <div class="table-row">
                            <div class="table-cell profile-image">
                                <div class="profile_image">
                                    {if jrProfile_is_profile_owner($_profile_id)}
                                        {jrCore_module_url module="jrProfile" assign="purl"}
                                        {jrCore_lang skin="kmSuperFans" id=5 default="Edit" assign="hover"}
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
                                    <span><a href="{$jamroom_url}/{$profile_url}">@{$profile_url}</a> </span>
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
            <div class="table">
                <div class="table-row">
                    <div class="table-cell">
                        {$menu_template = "profile_menu.tpl"}
                        {if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
                            {$menu_template = "profile_menu_mobile.tpl"}
                        {/if}
                        {jrProfile_menu template=$menu_template profile_quota_id=$profile_quota_id profile_url=$profile_url order="jrAction,jrBlog,jrCombinedAudio,jrAudio,jrCombinedVideo,jrVideo,jrGallery,jrGroup,jrEvent,jrYouTube,jrVimeo,jrFlickr"}
                    </div>
                    <div class="table-cell" style="width: 20px; white-space: nowrap; padding: 0 10px;">
                        {jrCore_lang id=5 skin="kmSuperFans" default="Follow" assign="Follow"}
                        {jrFollower_button profile_id=$_profile_id title=$follow}
                        {jrCore_lang skin="kmSuperFans" id=5 default="Edit" assign="edit"}
                        {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title=$edit}
                        {if jrUser_is_admin() || jrUser_is_power_user()}
                            {jrCore_lang skin="kmSuperFans" id="6" default="Create Profile" assign="create"}
                            {jrCore_item_create_button module="jrProfile" view="create" profile_id=$_profile_id title=$create}
                        {/if}
                        {jrProfile_delete_button profile_id=$_profile_id}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row profile_body">

