{jrCore_module_url module="jrBundle" assign="fcburl"}
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
                        {jrCore_item_list_buttons module="jrBundle" field="bundle" quantity_max="1" item=$item}
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:12px;">
                {if isset($item.bundle_image_size)}
                <div class="col3">
                    <div class="block_image p5">
                        {jrCore_module_function function="jrImage_display" module="jrBundle" type="bundle_image" item_id=$item._item_id size="large" crop="auto" alt=$item.bundle_title width=false height=false class="iloutline img_scale"}
                    </div>
                </div>
                <div class="col7">
                {else}
                <div class="col10">
                {/if}
                    <div class="p5">
                        <h3>{jrCore_lang module="jrBundle" id="43" default="Includes the following items"}:</h3><br>
                        {if is_array($item.bundle_items)}
                        {foreach $item.bundle_items as $_i}
                            <div class="bundle-item" style="float:left; width:49%">
                                <div class="table">
                                    <div class="table-row">
                                        <div class="table-cell img" >
                                            {jrCore_module_function function="jrImage_display" module=$_i.bundle_module type=$_i.item_image_type item_id=$_i._item_id size="medium" crop="auto" class="img_scale" alt=$_i.item_title alt=$_i.item_title width=96 height=96}
                                        </div>
                                        <div class="table-cell">
                                            <a href="{$_i.item_url}">{$_i.item_title}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                        {/if}
                    </div>
                </div>
                <div class="col2 last">
                    <div class="p5 center">
                    {* show savings if we can *}
                    {if isset($item.bundle_item_savings) && $item.bundle_item_savings > 0}
                        <h2><b>{jrCore_lang module="jrBundle" id="44" default="Save"}<br>&#36;{$item.bundle_item_savings|number_format:2}</b></h2>
                    {/if}
                    </div>
                </div>
            </div>

        </div>

    </div>
    {/foreach}

{/if}