{jrCore_include template="meta.tpl"}
<body>
<div id="header">
    <div class="menu_pad">
        <div id="header_content" class="table">
            <div class="table-row">
                <div class="table-cell mobile">
                    <ul>
                        <li class="mobile" id="menu_button"><a href="#menu2"></a></li>
                    </ul>
                </div>
                <div class="table-cell logo">
                    <a href="{$jamroom_url}">{jrCore_image image="logo.png" width="120px" height="auto"}</a>
                </div>
                <div class="table-cell mobile_size">
                    {jrCore_include template="menu_main.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="menu_side.tpl"}

{* This is the search form - shows as a modal window when the search icon is clicked on *}
<div id="searchform" class="search_box {$chameleon_style}" style="display:none;">
    {jrCore_lang module="jrSearch" id="1" default="Search Site" assign="st"}
    {jrSearch_form class="form_text" value=$st style="width:70%"}
    <div style="float:right;clear:both;margin-top:3px;">
        <a class="simplemodal-close">{jrCore_icon icon="close" size=20}</a>
    </div>
    <div class="clear"></div>
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
