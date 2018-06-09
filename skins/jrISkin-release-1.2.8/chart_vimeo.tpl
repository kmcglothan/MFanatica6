{if isset($_items)}
    {jrCore_module_url module="jrVimeo" assign="murl"}
    {foreach from=$_items item="item"}

        <div class="list_item">
            <div class="wrap clearfix">
                <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.vimeo_title_url}">{$item.vimeo_title}</a></span>
                <div class="external_image">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.vimeo_title_url}"><img src="{$item.vimeo_artwork_url}" class="img_scale"></a>
                </div>
                <div class="data clearfix">
                    <span>{$item.vimeo_comment_count|jrCore_number_format} {jrCore_lang skin="jrISkin" id="109" default="Comments"}</span>
                    <span>{$item.vimeo_like_count|jrCore_number_format} {jrCore_lang skin="jrISkin" id="110" default="Likes"}</span>
                </div>
            </div>
        </div>

    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
