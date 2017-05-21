{if isset($_items)}
    <div class="jrdocs_sidemenu">
        {jrCore_module_url module="jrDocs" assign="murl"}
        <ul class="doc_menu_list" style="list-style:none outside none;padding-left:0;">
            {foreach $_items as $_doc}
                <li {if $_doc._item_id == $_post._2}class="doc_menu_active"{/if} title="{$_doc.doc_title}">
                    {if $_doc.doc_level == "1"}
                        <div class="doc_indent_1"><a href="{$jamroom_url}/{$_post.module_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}" >{$_doc.doc_title}</a></div>
                    {elseif $_doc.doc_level == "2"}
                        <div class="doc_indent_2"><a href="{$jamroom_url}/{$_post.module_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}">{$_doc.doc_title}</a></div>
                    {else}
                        <div class="doc_indent_3"><a href="{$jamroom_url}/{$_post.module_url}/{$murl}/{$_doc.doc_category_url}/{$_doc._item_id}/{$_doc.doc_title_url}">{$_doc.doc_title}</a></div>
                    {/if}
                </li>
            {/foreach}
        </ul>
    </div>
{/if}
