{if isset($_items)}
    {jrCore_module_url module="jrVimeo" assign="murl"}
    {foreach from=$_items item="item"}

        <div class="list_item">
            <div class="wrap clearfix">
                <div class="row">
                    <div class="col4">
                        <div class="image">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.vimeo_title_url}"><img src="{$item.vimeo_artwork_url}" class="img_scale"></a>
                        </div>
                    </div>
                    <div class="col8">
                        <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.vimeo_title_url}">{$item.vimeo_title}</a></span>
                        <span class="date">{$item.vimeo_duration}</span>
                        <div class="list_buttons">
                            {jrCore_item_list_buttons module="jrVimeo" item=$item}
                        </div>
                        <div class="data clearfix">
                            <span>{$item.vimeo_comment_count|jrCore_number_format} {jrCore_lang skin="jrBeatSlinger" id="109" default="Comments"}</span>
                            <span>{$item.vimeo_like_count|jrCore_number_format} {jrCore_lang skin="jrBeatSlinger" id="110" default="Likes"}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
