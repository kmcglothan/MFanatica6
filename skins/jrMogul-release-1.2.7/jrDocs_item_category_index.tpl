{jrCore_module_url module="jrDocs" assign="murl"}
{*page title*}
{jrCore_lang module="jrDocs" id="53" default="Documentation" assign="lang_documentaton"}
{jrCore_page_title title="`$lang_documentaton` - `$category` - `$profile_name`"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMogul_breadcrumbs module="jrDocs" profile_url=$profile_url profile_name=$profile_name page="group" item=$_items[0]}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrDocs" profile_id=$_profile_id}
    </div>
</div>


<div class="box">
    {jrMogul_sort template="icons.tpl" nav_mode="jrDocs" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div class="media">
                <div class="wrap">
                    <div id="list">
                        {if isset($search_string_value) && $found_documents === 0}
                        <div id="doc_search" class="block_content">
                        {else}
                            <div id="doc_search" class="block_content" style="display:none; margin: 0 0 1em;">
                        {/if}
                            <div class="item">
                                {if isset($search_string_value) && $found_documents === 0}
                                    <div class="item error">{jrCore_lang module="jrDocs" id="62" default="There were no topics found that matched your search term"}</div>
                                {/if}
                                <form id="doc_search_form" method="get" action="{$jamroom_url}/{$profile_url}/{$murl}/{$category_url}">
                                    <input type="text" id="doc_search_text" name="search_string" value="{$search_string_value}" class="form_text form_search_text" tabindex="1" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) { jrDocs_search_submit(); }"><br><br>
                                    <img id="form_submit_indicator" src="{$jamroom_url}/skins/jrMogul/img/submit.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}"><input type="button" id="doc_search_submit" class="form_button" value="search" tabindex="2" onclick="jrDocs_search_submit();">
                                </form>
                            </div>
                        </div>

                        <div class="block_content">

                            {jrCore_module_url module="jrDocs" assign="murl"}
                            {if isset($_items)}
                                <div class="item">
                                    <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
                                        {foreach $_items as $_doc}
                                            <li data-id="{$_doc._item_id}">
                                                {if $_doc.doc_level == "1"}
                                                    <span class="doc_indent_1"><h2><a
                                                                    href="{$jamroom_url}/{$profile_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}">{$_doc.doc_title}</a>
                                                        </h2></span>
                                                {elseif $_doc.doc_level == "2"}
                                                    <span class="doc_indent_2"><h3><a
                                                                    href="{$jamroom_url}/{$profile_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}">{$_doc.doc_title}</a>
                                                        </h3></span>
                                                {else}
                                                    <span class="doc_indent_3"><h4>&bull; <a
                                                                    href="{$jamroom_url}/{$profile_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}">{$_doc.doc_title}</a>
                                                        </h4></span>
                                                {/if}
                                                {if isset($_doc.doc_snippet) && strlen($_doc.doc_snippet) > 0}
                                                    <br>
                                                    <div class="p10">&quot;... {$_doc.doc_snippet} ...&quot;</div>
                                                {/if}
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                        </div>
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