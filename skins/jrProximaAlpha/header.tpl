{jrCore_include template="meta.tpl"}

<body>

{if jrCore_is_mobile_device()}
    {jrCore_include template="header_menu_mobile.tpl"}
{/if}

<div id="header">
    <div id="header_content">

        {* Logo *}
        {if jrCore_is_mobile_device()}

            <div id="main_logo">
            {jrCore_image id="mmt" skin="jrProximaAlpha" image="menu.png" alt="menu"} <span>{$_conf.jrCore_system_name}</span>
            </div>

        {else}

            <div id="main_logo">
            <a href="{$jamroom_url}">{$_conf.jrCore_system_name}</a>
            </div>

            {jrCore_include template="header_menu_desktop.tpl"}

        {/if}

    </div>
</div>

{if $show_main_box === 1}

    <div id="main-bg">
        <div id="main-box">
            <h1 id="mb-headline">{$_conf.jrProximaAlpha_mb_headline}</h1>
            <p id="mb-text">{$_conf.jrProximaAlpha_mb_text}</p>

            {* App Store Icons *}
            <div id="main-app-stores">
            {if $_conf.jrProximaAlpha_apple_active == 'on'}
                <a href="{$_conf.jrProximaAlpha_apple_url|default:""}">{jrCore_image image="as-apple.png" width="200" height="60" class="app-store-img" alt="Available on the Apple App Store"}</a>
            {/if}
            {if $_conf.jrProximaAlpha_google_active == 'on'}
                <a href="{$_conf.jrProximaAlpha_google_url|default:""}">{jrCore_image image="as-google.png" width="200" height="60" class="app-store-img" alt="Available on the Google Play Store"}</a>
            {/if}
            {if $_conf.jrProximaAlpha_windows_active == 'on'}
                <a href="{$_conf.jrProximaAlpha_windows_url|default:""}">{jrCore_image image="as-windows.png" width="200" height="60" class="app-store-img" alt="Available on the Windows App Store"}</a>
            {/if}
            </div>

        </div>
    </div>

    <div id="wrapper" style="padding-top:0">

{else}

    <div id="wrapper" class="wrapper-bg">

{/if}

    {if $show_main_box === 1}
    <div id="index-container">
    {/if}

    <div id="content">
    <!-- end header.tpl -->
