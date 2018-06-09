{jrCore_module_url module="jrDocs" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}

    <div class="item">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.profile_name title=$item.profile_name}</a>
                    </div>
                </div>
                <div class="col10 last">
                    <div class="p5" style="overflow-wrap:break-word">
                        <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.doc_category_url}/{$item._item_id}/{$item.doc_title_url}">{$item.doc_title}</a></h2>
                        <span class="info">{jrCore_lang module="jrDocs" id="4" default="category"}:</span> <span class="info_c"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.doc_category_url}">{$item.doc_category}</a></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
