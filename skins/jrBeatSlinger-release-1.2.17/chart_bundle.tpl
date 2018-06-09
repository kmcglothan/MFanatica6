{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}

{if isset($_items)}
    {foreach from=$_items item="item"}
        {jrBeatSlinger_process_item item=$item module="jrFoxyCartBundle" assign="_item"}
        <div class="list_item">
            <div class="wrap">
                <div class="title">
                    <a href="{$_item.url}">
                        {$_item.title|truncate:55}
                    </a>
                </div>
                <a href="{$_item.url}">
                    {jrCore_module_function function="jrImage_stacked_image" module="{$item.stacked_image_module}" type="{$item.stacked_image_type}" item_id="{$item.stacked_image_item_id}" size="icon" alt="{$item.bundle_title}" border_width=0}
                </a>

                <div style="float:right;">
                    {if isset($item.bundle_item_savings) && $item.bundle_item_savings > 0}
                        <h3>{jrCore_lang module="jrFoxyCartBundle" id="44" default="Save"}<br>&#36;{$item.bundle_item_savings|number_format:2}</h3>
                    {/if}
                </div>
            </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}