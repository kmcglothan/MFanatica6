{jrCore_module_url module="jrFoxyCartBundle" assign="fcburl"}
{if isset($_items)}

    {foreach from=$_items item="item"}

    <div class="item">

        <div class="container">

            <div class="row">
                <div class="col8">
                    <div class="p5">
                        <h1><a href="{$jamroom_url}/{$item.profile_url}/{$fcburl}/{$item._item_id}/{$item.bundle_title_url}">{$item.bundle_title}</a></h1>
                    </div>
                </div>
                <div class="col4 last">
                    <div class="block_config">
                        {jrCore_item_list_buttons module="jrFoxyCartBundle" field="bundle" quantity_max="1" item=$item}
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:12px;">
                <div class="col3">
                    <div class="block_image">
                        {jrCore_module_function function="jrImage_stacked_image" module="{$item.stacked_image_module}" type="{$item.stacked_image_type}" item_id="{$item.stacked_image_item_id}" size="icon" alt="{$item.bundle_title}" border_width=0}
                    </div>
                </div>
                <div class="col8">
                    <div class="p5 pl10">
                        <h2>{jrCore_lang module="jrFoxyCartBundle" id="43" default="Includes the following items"}:</h2><br>
                        {if is_array($item.bundle_items)}
                        {foreach $item.bundle_items as $_i}
                            <div style="float:left; width:49%"><h3>&bull; <a href="{$_i.item_url}">{$_i.item_title}</a></h3></div>
                        {/foreach}
                        {/if}
                    </div>
                </div>
                <div class="col1 last">
                    <div class="p5 center">
                    {* show savings if we can *}
                    {if isset($item.bundle_item_savings) && $item.bundle_item_savings > 0}
                        <h2>{jrCore_lang module="jrFoxyCartBundle" id="44" default="Save"}<br>&#36;{$item.bundle_item_savings|number_format:2}</h2>
                    {/if}
                    </div>
                </div>
            </div>

        </div>

    </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}