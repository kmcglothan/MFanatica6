{$page_template = "profile"}
{jrCore_include template="header.tpl"}
{jrCore_module_url module="jrProfile" assign="murl"}

<section id="profile">

    {* set up default crop *}
    {$crop = "8:3"}
    {if jrCore_is_mobile_device()}
        {$crop = "3:2"}
    {/if}

    {* by default all things show *}
    {$show = true}
    {$menu = true}

    {* lets check our profile_header config settings *}
    {if $_conf.jrBeatSlinger_show_header == 'minimal' && $profile_disable_header == '1'}
        {* we're not showing the header on profile module pages *}
        {$show = false}
        {if $_conf.jrBeatSlinger_show_menu != 'on'}
            {* we're not showing the menu either *}
            {$menu = false}
        {/if}

        {* we must be showing a header *}
    {elseif $profile_disable_header == '1'}
        {* let's see if it's full or minimal. If it's full we just skip this section *}
        {if $_conf.jrBeatSlinger_show_header == 'compact'}
            {$crop = "9:1"}
        {/if}
        {if jrCore_is_mobile_device()}
            {$crop = "3:1"}
        {/if}
    {/if}

    {if $show != false}
        <div id="profile_header">
            <div class="clearfix" style="position: relative;">
                {if $profile_header_image_size > 0}
                    <a href="{$jamroom_url}/{$murl}/image/profile_header_image/{$_profile_id}/1280" data-lightbox="profile_header" title="{jrCore_lang skin="jrBeatSlinger" id="34" default="Click to view"}">
                        {jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_header_image" item_id=$_profile_id size="1280" class="img_scale" crop=$crop alt=$profile_name _v=$profile_header_image_time}
                    </a>
                {else}
                    {jrCore_image image="profile_header_image.jpg" width="1140" class="img_scale" height="auto"}
                {/if}
                <div class="profile_hover"></div>
                {if jrProfile_is_profile_owner($_profile_id)}
                    <div class="profile_admin_buttons">
                        <div class="row">
                            <div class="col6">
                                <div class="wrap">
                                    <a class="camera" href="{$_conf.jrCore_base_url}/{$murl}/settings/profile_id={$_profile_id}">
                                        {jrCore_icon icon="camera2" size="32" color="ffffff"}
                                        {jrCore_lang skin="jrBeatSlinger" id="124" default="Update Cover Image"}
                                    </a>
                                </div>
                            </div>
                            <div class="col6">
                                <div class="wrap" style="text-align: right">

                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="profile_info">
                    <div class="wrap">
                        <div class="profile_image">
                            {if jrProfile_is_profile_owner($_profile_id)}
                                {jrCore_module_url module="jrProfile" assign="purl"}
                                {jrCore_lang skin="jrBeatSlinger" id="27" default="Change Image" assign="hover"}
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
                                    <span class="normal">{$hover}</span>&nbsp;{jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}
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
                        <div class="profile_name">
                            {$profile_name|truncate:55}<br>
                            <span><a href="{$jamroom_url}/{$profile_url}">@{$profile_url}</a> </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {if $menu != false}
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
                            {if jrUser_is_logged_in()}
                                {if !jrUser_is_linked_to_profile($_profile_id)}
                                    {if jrCore_module_is_active('jrContact')}
                                        <a title="Message This Profile">{jrCore_icon icon="mail" size="22" class="email"}</a>
                                    {/if}
                                    {if jrCore_module_is_active('jrAction')}
                                        <a title="Mention This Profile" onclick="jrBeatSlinger_modal('#mention_modal','@{$profile_url}')">{jrCore_icon icon="mention" size="22" class="email"}</a>
                                        <div class="mention-modal" id="mention_modal" style="display: none">
                                            {jrAction_form quick_share=false editor=false}<br>
                                            <div style="text-align: right">
                                                {jrCore_icon icon="close" size="16" class="simplemodal-close"}
                                            </div>
                                        </div>
                                    {/if}
                                {else}
                                    {if jrCore_module_is_active('jrContact')}
                                        <a title="Check Messages">{jrCore_icon icon="mail" size="22" class="email"}</a>
                                    {/if}
                                {/if}
                            {/if}


                            {jrCore_lang id=24 skin="jrBeatSlinger" default="Follow" assign="follow"}
                            {jrFollower_button profile_id=$_profile_id title=$follow}
                            {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Edit Profile"}
                            {if jrUser_is_admin() || jrUser_is_power_user()}
                                {jrCore_item_create_button module="jrProfile" view="create" profile_id=$_profile_id title="Create Profile"}
                            {/if}
                            {jrProfile_delete_button profile_id=$_profile_id}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    {/if}


    <div class="row" style="margin-top: 10px;min-height: 600px;">

