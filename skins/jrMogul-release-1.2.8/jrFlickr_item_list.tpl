{jrCore_module_url module="jrFlickr" assign="murl"}
{if isset($_items)}
    <div class="row">
    {foreach $_items as $item}
        {assign var="_data" value=$item.flickr_data|json_decode:true}
        <div class="col4">
            <div class="wrap">
                <div class="list_item">
                    <div class="wrap">
                        <div class="external_image">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.flickr_title_url}">
                                <img class="img_scale" src="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}.jpg" alt="{$item.flickr_title|jrCore_entity_string}"></a><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
    </div>
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
