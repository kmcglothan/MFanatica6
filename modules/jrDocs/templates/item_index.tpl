{jrCore_module_url module="jrDocs" assign="murl"}

<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrDocs" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrDocs" id="53" default="Documentation"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrDocs" id="53" default="Documentation"}</a>
        </div>
    </div>

    <div class="block_content">
        {* Show Categories *}
        {jrDocs_categories profile_id=$_profile_id assign="_cats"}
        {if is_array($_cats)}
            <div class="item">
            {jrCore_module_url module="jrDocs" assign="murl"}
            <ol class="sortable list doc_chapter_list">
                {foreach $_cats as $_doc}
                <li data-cat-url="{$_doc.doc_category_url}">
                    <h1><a href="{$jamroom_url}/{$_doc.profile_url}/{$murl}/{$_doc.doc_category_url}">{$_doc.doc_category}</a></h1>
                </li>
                {/foreach}
            </ol>
            </div>
        {/if}
    </div>

</div>


{* We want to allow the item owner to re-order *}
{if jrProfile_is_profile_owner($_profile_id)}

    <style type="text/css">
    .sortable {
        margin: auto;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .sortable li {
        cursor: move;
        margin-left: 40px;
        padding: 10px;
    }
    li.sortable-placeholder {
        background: none;
        height: 32px;
        margin: 12px;
    }
    </style>

    <script>
        $(function() {
            $('.sortable').sortable().bind('sortupdate', function(event,ui) {
                //Triggered when the user stopped sorting and the DOM position has changed.
                var o = $('ol.sortable li').map(function(){
                    return $(this).data("cat-url");
                }).get();
                $.post(core_system_url + '/' + jrDocs_url + "/chapter_order_update/__ajax=1", {
                    chapter_order: o,
                    profile_id: '{$_profile_id}'
                });
            });
        });
    </script>

{/if}