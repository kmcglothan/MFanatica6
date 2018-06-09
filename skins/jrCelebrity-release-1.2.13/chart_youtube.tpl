{jrCore_module_url module="jrYouTube" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}
        <div class="list_item">
            <div class="wrap clearfix">
                <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}">{$item.youtube_title}</a></span>
                <div class="external_image">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}"><img class="img_scale" src="{$item.youtube_artwork_url}" alt="{$item.youtube_title|jrCore_entity_string}"></a>
                </div>
                <div class="data clearfix">
                    <span>{$item.youtube_comment_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="109" default="Comments"}</span>
                    <span>{$item.youtube_like_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="110" default="Likes"}</span>
                </div>
            </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}