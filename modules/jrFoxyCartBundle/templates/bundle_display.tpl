{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}
{if isset($item) && strlen($item_info.add_to_cart_url) > 0}
    <div class="bundle_item_section">
        <table width="100%" class="bundle_item_table">
            <tr>
                <td style="width:80%;">{$item_info.item_title}</td>
                <td style="width:20%;white-space:nowrap">
                    {jrCore_module_function function="jrFoxyCart_add_to_cart" module=$module field=$field item=$item no_bundle="true" onclick="jrFoxyCartBundle_close()"}
                </td>
            </tr>
        </table>
    </div>
    <div class="bundle_drop_section bundle_drop_top">
{else}
    <div class="bundle_drop_section">
{/if}

{if isset($_items) && is_array($_items)}
    <h2>{jrCore_lang module="jrFoxyCartBundle" id="42" default="Special Bundle Deals for this item"}:</h2>
    <table width="100%" class="bundle_table">
        {foreach $_items as $_bn}
        <tr>
            <td style="width:80%">
                {if isset($_bn.bundle_module)}
                <a href="{$_conf.jrCore_base_url}/{$_bn.profile_url}/{$murl}/{$_bn._item_id}/{$_bn.bundle_title_url}">{$_bn.bundle_title} ({jrCore_lang module="jrFoxyCartBundle" id="21" default="album"})</a>
                {else}
                <a href="{$_conf.jrCore_base_url}/{$_bn.profile_url}/{$murl}/{$_bn._item_id}/{$_bn.bundle_title_url}">{$_bn.bundle_title}</a>
                {/if}
            </td>
            <td class="right" style="width:20%;white-space:nowrap;padding:3px 0;">
                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$bn.bundle_item_price no_bundle="true" item=$_bn onclick="jrFoxyCartBundle_close()"}
            </td>
        </tr>
        {/foreach}
    </table>
</div>
{/if}

<div style="float:right;clear:both;margin-top:3px;">
    <a onclick="jrFoxyCartBundle_close();">{jrCore_icon icon="close" size="16"}</a>
</div>
