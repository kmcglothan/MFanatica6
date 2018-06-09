{jrCore_module_url module="jrDocs" assign="murl"}
{jrCore_lang module="jrDocs" id=53 default="Documentation" assign="lang_documentaton"}
{jrCore_lang module="jrDocs" id=54 default="Table of Contents" assign="toc_lang"}
{jrCore_page_title title="`$lang_documentaton` - `$category` - `$profile_name`"}


<div class="page_nav">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrDocs" profile_url=$profile_url page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrDocs" profile_id=$_profile_id}
    </div>
</div>


<div class="box">
    {jrCelebrity_sort template="icons.tpl" nav_mode="jrDocs" profile_url=$profile_url}

    <div class="box_body">
        <div class="wrap">
            <div id="list">
                {if isset($search_string_value) && $found_documents === 0}
                <div id="doc_search" class="block_content">
                    {else}
                    <div id="doc_search" class="block_content" style="display:none">
                        {/if}
                        <div class="item">
                            {if isset($search_string_value) && $found_documents === 0}
                                <div class="item error">{jrCore_lang module="jrDocs" id="62" default="There were no topics found that matched your search term"}</div>
                            {/if}
                            <form id="doc_search_form" method="get"
                                  action="{$jamroom_url}/{$profile_url}/{$murl}/contents">
                                <input type="text" id="doc_search_text" name="search_string"
                                       value="{$search_string_value}" class="form_text form_search_text"
                                       tabindex="{jrCore_next_tabindex}"
                                       onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) { jrDocs_search_submit(); }"><br><br>
                                <img id="form_submit_indicator"
                                     src="{$jamroom_url}/skins/jrCelebrity/img/submit.gif"
                                     width="24" height="24"
                                     alt="{jrCore_lang module="jrCore" id="73" default="working..."}"><input
                                        type="button" id="doc_search_submit" class="form_button" value="search"
                                        tabindex="2" onclick="jrDocs_search_submit();">
                            </form>
                        </div>
                    </div>

                    <div class="item" style="margin: 0;">

                        {jrCore_module_url module="jrDocs" assign="murl"}
                        {if isset($_items)}
                            {$i = 1}
                            <ul class="doc_toc_list">
                                {foreach $_items as $_chapter}
                                    {$_info = array_slice($_chapter, 0, 1)}
                                <li>{$_info = array_shift($_info)}
                                    <li data-cat_url="{$_doc._item_id}" class="toc_chapter"><h1>{$i}. {$_info.doc_category}</h1></li>
                                    {$i = $i + 1}
                                    <ul class="sortable list toc" style="list-style:none outside none;padding-left:0;">
                                        {foreach $_chapter as $_doc}
                                            <li data-id="{$_doc._item_id}" class="highlight_{$_doc.doc_group}">
                                                {if $_doc.doc_level == "1"}
                                                    <h2 class="toc_indent_1"><a href="{$jamroom_url}/{$profile_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}">{$_doc.doc_title}</a></h2>
                                                {elseif $_doc.doc_level == "2"}
                                                    <h3 class="toc_indent_2"><a href="{$jamroom_url}/{$profile_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}">{$_doc.doc_title}</a></h3>
                                                {else}
                                                    <h4 class="toc_indent_3"><a href="{$jamroom_url}/{$profile_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}">{$_doc.doc_title}</a></h4>
                                                {/if}
                                            </li>
                                        {/foreach}
                                    </ul>
                                </li>
                            {/foreach}
                            </ul>
                        {/if}
                    </div>
                </div>
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
    <style type="text/css" media="screen">
        .highlight_admin h2,
        .highlight_admin h3,
        .highlight_admin h4 {
            background-color: #fff2bf;
        }

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
            list-style: none;
            cursor: move;
        }

        li.sortable-placeholder {
            border: 1px dashed #BBB;
            background: none;
            height: 20px;
            margin: 12px;
        }
    </style>
    <script>
        $(function () {
            $('.sortable').sortable().bind('sortupdate', function (event, ui) {
                //Triggered when the user stopped sorting and the DOM position has changed.
                var o = $('ul.sortable li').map(function () {
                    return $(this).data("id");
                }).get();
                $.post(core_system_url + '/' + jrDocs_url + "/category_order_update/__ajax=1", {
                    doc_order: o
                });
            });
        });
    </script>
{/if}