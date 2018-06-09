{jrCore_page_title title=$_page.page_title}
{jrCore_include template="header.tpl"}

<div class="container sb-page-id-{$_page.page_id}">

{if jrUser_is_master()}
    {if $_widget_count == 0 && $show_widget_notice}
        <div id="sb-empty-notice" class="p20 center"><strong>No Widgets have been added to this page yet</strong><br><br>Click the <strong>Site Builder</strong> button to get started.</div>
    {/if}
    <ul class="sb-widget-sortable" id="page_container">
{/if}

{$page_content}

{if jrUser_is_master()}
    </ul>
{/if}


</div>


{if jrUser_is_master()}

<style type="text/css">
    ul.sb-widget-sortable {
        list-style: none outside none;
        margin: auto;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    ul.sb-widget-sortable > li {
        list-style: none;
    }
    ul.sb-widget-sortable > li.sortable-placeholder {
        border: 2px dashed #FC0;
        background: none;
        height: 38px;
    }
    .sb-editing_active .sb-drag-handle {
        cursor: move;
    }
</style>

{/if}

{jrCore_include template="footer.tpl"}