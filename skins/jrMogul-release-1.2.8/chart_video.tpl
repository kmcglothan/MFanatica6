{if isset($_items)}

    {foreach from=$_items item="item"}

    <div class="list_item">
        <div class="wrap clearfix">
            {if strlen($item.video_title) > 0}
                {jrCore_module_url module="jrVideo" assign="murl"}
                <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">{$item.video_title}</a></span>
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">
                    {jrCore_module_function
                    function="jrImage_display"
                    module="jrVideo"
                    type="video_image"
                    item_id=$item._item_id
                    size="xlarge"
                    crop="16:9"
                    class="iloutline img_scale"
                    alt=$item.video_title
                    width=false
                    height=false}</a>
                <div class="data clearfix">
                    <span>{$item.video_comment_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="109" default="Comments"}</span>
                    <span>{$item.video_like_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="110" default="Likes"}</span>
                </div>
            {elseif strlen($item.vimeo_title) > 0}
                {jrCore_module_url module="jrVimeo" assign="murl"}
                <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.vimeo_title_url}">{$item.vimeo_title}</a></span>
                <div class="external_image">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.vimeo_title_url}"><img src="{$item.vimeo_artwork_url}" class="img_scale"></a>
                </div>
                <div class="data clearfix">
                    <span>{$item.vimeo_comment_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="109" default="Comments"}</span>
                    <span>{$item.vimeo_like_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="110" default="Likes"}</span>
                </div>
            {else}
                {jrCore_module_url module="jrYouTube" assign="murl"}
                <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}">{$item.youtube_title}</a></span>
                <div class="external_image">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}"><img class="img_scale" src="{$item.youtube_artwork_url}" alt="{$item.youtube_title|jrCore_entity_string}"></a>
                </div>
                <div class="data clearfix">
                    <span>{$item.youtube_comment_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="109" default="Comments"}</span>
                    <span>{$item.youtube_like_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="110" default="Likes"}</span>
                </div>
            {/if}
        </div>
    </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}