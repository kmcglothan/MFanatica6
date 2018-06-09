{jrCore_module_url module="jrDocs" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrDocs" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrDocs" profile_id=$_profile_id}
    </div>
</div>


<div class="box">
    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrDocs" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div id="list">
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
    </div>
</div>
<style>
    table.page_content {
        display: none;
    }
    section#profile .col8 > div > .block {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
        border: medium none;
        border-radius: 0;
        box-shadow: none;
        margin: 0;
        padding: 0;
    }
</style>
{* We want to allow the item owner to re-order *}
{if jrProfile_is_profile_owner($_profile_id)}

    <style type="text/css">
    .sortable {
        margin: auto;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
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