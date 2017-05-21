{if $_conf.jrSiteBuilder_enabled == 'on'}
<div id="sb-include-section">

    <script type="text/javascript" src="{$jamroom_url}/modules/jrCore/contrib/tinymce/tinymce.min.js?v={$_mods.jrCore.module_version}"></script>
    <link rel="stylesheet" href="{$jamroom_url}/modules/jrCore/contrib/codemirror/lib/codemirror.css" media="screen" />
    <script type="text/javascript" src="{$jamroom_url}/modules/jrCore/contrib/codemirror/lib/codemirror.js?v={$_mods.jrCore.module_version}"></script>
    <script type="text/javascript" src="{$jamroom_url}/modules/jrCore/contrib/codemirror/mode/smarty/smarty.js?v={$_mods.jrCore.module_version}"></script>
    <script type="text/javascript" src="{$jamroom_url}/modules/jrSiteBuilder/js/jquery.nouislider.min.js?v={$_mods.jrSiteBuilder.module_version}"></script>
    {jrSiteBuilder_tinymce_init}
    <script type="text/javascript">
        var cm; //for codemirror editor.
    </script>


    <div id="sb-layout-section">
        <div id="sb-doc-menu" class="sb-button" onclick="window.open('https://www.jamroom.net/r/site-builder-help');">Help</div>
        <div id="sb-edit-menu" class="sb-button" onclick="jrSiteBuilder_edit_menu()">Menu Editor</div>
        <div id="sb-page-delete" class="sb-button" onclick="if (confirm('Are you sure you want to delete this page?')) { jrSiteBuilder_delete_page('{$page_id}') }">Delete Page</div>
        <div id="sb-edit-layout" class="sb-button" onclick="jrSiteBuilder_edit_layout('{$page_id}')">Page Config</div>
        <div id="sb-close-button" class="sb-button" onclick="jrSiteBuilder_close()">Close</div>
    </div>

    {if isset($notice)}
        <div id="sb-edit-menu" class="sb-button" style="bottom: 76px" onclick="jrSiteBuilder_edit_menu()">Menu Editor</div>
        <div id="sb-edit-button" class="sb-button" onclick="if(confirm('{$notice|jrCore_entity_string}')) { jrSiteBuilder_create_and_edit_page() }">Site Builder</div>
    {else}
        <div id="sb-edit-button" class="sb-button" onclick="jrSiteBuilder_edit_page('{$page_id}')">Site Builder</div>
    {/if}

    <div id="sb-edit-cp-holder">
        <div id="sb-edit-cp" class="block_content">
           {* Editor Appears Here *}
        </div>
    </div>

    {* Templates *}
    <link rel="stylesheet" property="stylesheet" href="{$jamroom_url}/core/icon_css/20?_v={$smarty.now}" />

</div>
{/if}
