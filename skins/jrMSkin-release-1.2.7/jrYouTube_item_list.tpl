{if isset($_items)}
    {jrCore_module_url module="jrYouTube" assign="murl"}
    {foreach from=$_items item="item"}

        <div class="list_item">
            <div class="wrap clearfix">
                <div class="row">
                    <div class="col4">
                        <div class="external_image" style="margin-right: 1em">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}"><img
                                        src="{$item.youtube_artwork_url}"
                                        alt="{$item.youtube_title|jrCore_entity_string}"
                                        class="img_scale"></a>
                        </div>
                    </div>
                    <div class="col8">
                        <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.youtube_title_url}">{$item.youtube_title}</a></span>

                        {if isset({$item.youtube_category}) && strlen({$item.youtube_category}) > 0}
                            <span class="date">{$item.youtube_category}</span>
                        {/if}
                        {if isset($item.youtube_duration) && strlen($item.youtube_duration) > 0}
                            <span class="date">{$item.youtube_duration}</span><br>
                        {/if}
                        <div class="list_buttons">
                            {jrCore_item_list_buttons module="jrYouTube" item=$item}
                        </div>
                        <div class="data clearfix">
                            <span>{$item.youtube_comment_count|jrCore_number_format} {jrCore_lang skin="jrMSkin" id="109" default="Comments"}</span>
                            <span>{$item.youtube_like_count|jrCore_number_format} {jrCore_lang skin="jrMSkin" id="110" default="Likes"}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
