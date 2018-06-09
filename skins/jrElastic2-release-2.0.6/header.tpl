{jrCore_include template="meta.tpl"}

<body>

<div id="header">
    <div id="header_content" class="clearfix">

        {* Logo *}
        {if jrCore_is_mobile_device()}
            <div id="main_logo">
                <div class="table">
                    <div class="table-row">
                        <div class="table-cell w40">
                            <a id="mmt">{jrCore_image skin="jrElastic2" image="menu.png" alt="menu" style="max-width:28px;max-height:28px;"}</a>
                        </div>
                        <div class="table-cell">
                           <a href="{$jamroom_url}"> {jrCore_image image="logo.png" width="170" height="40" class="jlogo" alt=$_conf.jrCore_system_name custom="logo" style="margin:auto;"}</a>
                        </div>
                        <div class="table-cell w40">
                            <div class="table">
                                <div class="table-row">
                                    <div class="table-cell">
                                        {if jrCore_module_is_active('jrSearch')}
                                            {jrCore_lang skin="jrElastic2" id="24" default="search" assign="st"}
                                            <a onclick="jrSearch_modal_form();" title="{$st}">{jrCore_image image="search44.png" width=28 height=28 alt=$st style="margin-top:5px;margin-right:10px"}</a>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        {else}
            <div id="main_logo">
                <a href="{$jamroom_url}">{jrCore_image image="logo.png" width="191" height="44" class="jlogo" alt=$_conf.jrCore_system_name custom="logo"}</a>
            </div>
            {jrCore_include template="header_menu_desktop.tpl"}

        {/if}

    </div>

</div>

{if jrCore_is_mobile_device()}
    {jrCore_include template="header_menu_mobile.tpl"}
{/if}

{* This is the search form - shows as a modal window when the search icon is clicked on *}
<div id="searchform" class="search_box" style="display:none;">
    {jrCore_lang module="jrSearch" id="1" default="Search Site" assign="st"}
    {jrSearch_form class="form_text" value=$st style="width:auto"}
    <div class="simplemodal-close">{jrCore_icon icon="close" size=16}</div>
</div>

<div id="wrapper">

    {if  strlen($page_template) == 0}
    <div id="content">
    {/if}

        <noscript>
            <div class="item error center" style="margin:12px">
                This site requires Javascript to function properly - please enable Javascript in your browser
            </div>
        </noscript>

        <!-- end header.tpl -->
