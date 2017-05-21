{assign var="from_profile_page" value="yes"}
{jrCore_include template="header.tpl"}


<div class="container">
{if isset($_conf.jrSoloArtist_main_id) && $_profile_id == $_conf.jrSoloArtist_main_id}
    <div class="row">
        <div class="col12 last">
            <div class="band_profile_image">
                <a href="{$jamroom_url}" title="{$_conf.jrCore_system_name}">{jrCore_image image="logo.png" width="1140" class="img_scale" alt=$_conf.jrCore_system_name custom="logo"}</a>
            </div>
        </div>
    </div>
{/if}
    {if isset($_conf.jrSoloArtist_main_id) && $_profile_id != $_conf.jrSoloArtist_main_id}
        <div class="profile_menu">
            <div class="profile_menu_content left">
                {if jrCore_is_mobile_device()}
                    {jrProfile_menu template="profile_menu_mobile.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
                {else}
                    {jrProfile_menu template="profile_menu.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
                {/if}
            </div>
        </div>
    {else}
        <div class="profile_menu">
            <div class="profile_menu_content left">
            {if isset($_conf.jrSoloArtist_admin_pro_menu) && $_conf.jrSoloArtist_admin_pro_menu == 'on'}
                {if jrCore_is_mobile_device()}
                    {jrProfile_menu template="profile_menu_mobile.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
                {else}
                    {jrProfile_menu template="profile_menu.tpl" profile_quota_id=$profile_quota_id profile_url=$profile_url}
                {/if}
            {else}
                &nbsp;
            {/if}
            </div>
        </div>
    {/if}
</div>

<div id="wrapper">
    <div id="content" style="margin-top: 0;">

        <!-- end header.tpl -->
<div class="container">

    {if isset($_conf.jrSoloArtist_main_id) && $_profile_id != $_conf.jrSoloArtist_main_id}
    <div class="row">
        <div class="col12 last">
            <div class="profile_name_box">
                <a href="{$jamroom_url}/{$profile_url}"><h1 class="profile_name">{$profile_name}</h1></a>
                <div class="profile_actions">

                    {jrCore_item_update_button module="jrProfile" view="settings/profile_id=`$_profile_id`" profile_id=$_profile_id item_id=$_profile_id title="Update Profile"}

                    {if jrUser_is_admin() || jrUser_is_power_user()}
                        {jrCore_item_create_button module="jrProfile" view="create" profile_id=$_profile_id title="Create new Profile"}
                    {/if}

                    {jrProfile_delete_button profile_id=$_profile_id}

                </div>
            </div>
        </div>
    </div>

    {/if}

    <div class="row">
        {* next <div> starts in body *}
