{jrCore_include template="meta.tpl"}

<body>

{if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
    {jrCore_include template="header_menu_mobile.tpl"}
{/if}

<div id="header">

    <div id="header_content">

        {if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
            {jrCore_image id="mmt" skin="jrSoloArtist" image="menu.png" alt="menu" style="max-width:28px;max-height:28px;"}

            <div class="block_config">
                {if jrCore_module_is_active('jrSearch')}
                    <a onclick="jrSearch_modal_form();" title="Site Search">{jrCore_image image="magnifying_glass.png" width="24" height="24" alt="search"}</a>&nbsp;
                {/if}

                {* Add in Cart link if jrFoxyCart module is installed *}
                {if jrCore_module_is_active('jrFoxyCart') && strlen($_conf.jrFoxyCart_api_key) > 0}
                    <a href="{$_conf.jrFoxyCart_store_domain}/cart?cart=view" title="{jrCore_lang skin=$_conf.jrCore_active_skin id="46" default="View"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="47" default="Cart"}">{jrCore_image image="cart.png" width="24" height="24" alt="cart"}</a>&nbsp;
                    <span id="fc_minicart"><span id="fc_quantity"></span></span>
                {/if}
            </div>

        {else}
            {jrCore_include template="header_menu_desktop.tpl"}
        {/if}

    </div>
    {if jrCore_is_mobile_device() || jrCore_is_tablet_device()}
        <div class="clear"></div>
    {/if}

</div>


    {if isset($selected) && $selected == 'home'}

        <div class="container">
            <div class="row">
                <div class="col12 last">
                    <div class="center" style="margin-top: 65px;">
                        <a href="{$jamroom_url}" title="{$_conf.jrCore_system_name}">{jrCore_image image="logo.png" class="img_scale" alt=$_conf.jrCore_system_name style="max_width:1140px;"}</a>
                    </div>
                </div>
            </div>
            <div class="player_bckgrd">
                <div class="row">
                    <div class="col12 last">
                        <div class="center p5 mb20">
                            {if isset($_conf.jrSoloArtist_index_album) && strlen($_conf.jrSoloArtist_index_album) > 0}
                                {jrCore_list module="jrAudio" order_by="audio_file_track numerical_asc" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" search2="audio_album = `$_conf.jrSoloArtist_index_album`" template="index_player.tpl"}
                            {elseif jrUser_is_master()}
                                <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="62" default="Enter an Album name to show a Featured Album player here!"}</h3> &nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrSoloArtist">{jrCore_image image="update.png" width="18" height="18" alt="Change Album" title="Change Album"}</a><br>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}

{* This is the search form - shows as a modal window when the search icon is clicked on *}
<div id="searchform" class="search_box" style="display:none;">
    <div style="float:right;"><input type="button" class="simplemodal-close form_button" value="x"></div>
    {jrCore_lang module="jrSearch" id="1" default="Search Site" assign="st"}
    <span class="title">{$_conf.jrCore_system_name} {$st}</span><br><br>
    {jrSearch_form class="form_text" value=$st style="width:70%"}
    <div class="clear"></div>
</div>

<div id="wrapper">
{if !isset($from_profile_page) || $from_profile_page != 'yes'}
    {if $_post._uri == '/profile/settings' || $_post._uri == '/user/account' || $_post._uri == '/user/notifications' || $_post._uri == '/foxycart/subscription_browser' || $_post._uri == '/foxycart/items' || $_post._uri == '/oneall/networks' || $_post._uri == '/profiletweaks/customize' || $_post._uri == '/follow/browse' || $_post._uri == '/note/notes'}
        {assign var="acp_menu_item" value="no"}
    {else}
        {assign var="acp_menu_item" value="yes"}
    {/if}
{/if}
    <div id="content">

        <!-- end header.tpl -->
