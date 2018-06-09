{jrCore_module_url module="jrBundle" assign="murl"}
{if isset($item) && strlen($item_info.add_to_cart_url) > 0 && $item_info.bundle_only != 'on'}
    <div class="bundle_item_section">
        <table width="100%" class="bundle_item_table">
            <tr>
                <td style="width:80%;">{$item_info.item_title}</td>
                <td style="width:20%;white-space:nowrap">
                    {jrPayment_add_to_cart_button module=$module item=$item field=$field no_bundle=true onclick="jrBundle_close()"}
                </td>
            </tr>
        </table>
    </div>
{/if}

{if isset($_items) && is_array($_items)}
<div class="bundle_drop_section bundle_drop_top">
    <h2>{jrCore_lang module="jrBundle" id=42 default="Bundles that include this item"}:</h2>
    <table width="100%" class="bundle_table">
        {foreach $_items as $_bn}
        <tr>
            <td style="width:80%">
                {if isset($_bn.bundle_module)}
                <a href="{$_conf.jrCore_base_url}/{$_bn.profile_url}/{$murl}/{$_bn._item_id}/{$_bn.bundle_title_url}">{$_bn.bundle_title} ({jrCore_lang module="jrBundle" id=21 default="album"})</a>
                {else}
                <a href="{$_conf.jrCore_base_url}/{$_bn.profile_url}/{$murl}/{$_bn._item_id}/{$_bn.bundle_title_url}">{$_bn.bundle_title}</a>
                {/if}
            </td>
            <td class="right" style="width:20%;white-space:nowrap;padding:3px 0;">
                {jrPayment_add_to_cart_button module="jrBundle" item=$_bn field="bundle" no_bundle=true onclick="jrBundle_close()"}
            </td>
        </tr>
        {/foreach}
    </table>
</div>
{/if}

<div style="float:right;clear:both;margin-top:3px;">
    <a onclick="jrBundle_close();">{jrCore_icon icon="close" size="16"}</a>
</div>
