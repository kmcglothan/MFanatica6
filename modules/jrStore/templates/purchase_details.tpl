{jrCore_include template="header.tpl"}

{jrCore_module_url module="jrStore" assign="murl"}
{jrCore_module_url module="jrFoxyCartBundle" assign="burl"}

{assign var="active_tab" value="details"}
{jrCore_include template="nav_tabs.tpl" module="jrStore"}

<h3>{jrCore_lang module="jrStore" id="66" default="Who the seller is:"}</h3>
<div class="block">
    <div style="overflow:hidden">
        <div style="float:left;padding-right:12px;">
            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$seller['_user_id'] size="small" alt='admin' class="action_item_user_img iloutline"}
        </div>
        <div>
            <table style="width: 80%">
                <tr>
                    <td style="width: 20%">{jrCore_lang module="jrStore" id="67" default="Profile Name"}</td>
                    <td><b><a href="{$jamroom_url}/{$seller['profile_url']}/">{$seller['profile_name']}</a></b></td>
                </tr>
                <tr>
                    <td>{jrCore_lang module="jrStore" id="68" default="Store Country"}</td>
                    <td>{$seller['profile_jrStore_store_country']}</td>
                </tr>
                {*<tr>*}
                    {*<td>Items Sold</td>*}
                    {*<td>250</td>*}
                {*</tr>*}
                {*<tr>*}
                    {*<td>Sellers rating</td>*}
                    {*<td>100 good / 4 bad</td>*}
                {*</tr>*}
            </table>

        </div>
    </div>
</div>
<h3>{jrCore_lang module="jrStore" id="69" default="What I'm getting from them:"}</h3><br>
<div class="block">
    <div style="overflow:hidden">
        <table>
            <tr>
                <th class="left">{jrCore_lang module="jrStore" id="70" default="Name"}</th>
                <th class="right">{jrCore_lang module="jrStore" id="71" default="Qty"}</th>
                <th class="center">{jrCore_lang module="jrStore" id="72" default="Bundle"}</th>
            </tr>
            {foreach $purchased_items as $_i}
            <tr>
                <td>{$_i['details'].product_title}</td>
                <td class="right">{$_i.purchase_qty}</td>
                <td class="center">{if strlen($_i['bundle'].bundle_title) > 2}<a href="{$jamroom_url}/{$_i['bundle'].profile_url}/{$burl}/{$_i['bundle']._item_id}/{$_i['bundle'].bundle_title_url}">{$_i['bundle'].bundle_title}</a>{else}{jrCore_lang module="jrStore" id="73" default="(not part of a bundle)"}{/if}</td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>
<h3>{jrCore_lang module="jrStore" id="74" default="Where it is shipping from:"}</h3><br>
<div class="block">
    <div style="overflow:hidden">
{$seller['profile_jrStore_store_details']|nl2br}
    </div>
</div>
{*<h3>The estimated delivery date:</h3><br>*}
{*<div class="block">*}
    {*<div style="overflow:hidden">*}
        {*13 June 2013*}
    {*</div>*}
{*</div>*}
<h3>{jrCore_lang module="jrStore" id="75" default="Status of the order:"}</h3>
<div class="block">
    {jrCore_lang module="jrStore" id="76" default="Status:"} {$status_status}
</div>
<br>
<h3>{jrCore_lang module="jrStore" id="77" default="That my address is correct:"}</h3><br>
<div class="shipping_label">
    <span class="l1">{$txn_shipping_first_name} {$txn_shipping_last_name}</span><br>
    <span class="l2">{$txn_shipping_company}</span><br>
    <span class="l3">{$txn_shipping_address1}</span><br>
    <span class="l4">{$txn_shipping_address2}</span><br>
    <span class="l5">{$txn_shipping_city}</span><br>
    <span class="l6">{$txn_shipping_country_name}</span>
    <span class="l7">{$txn_shipping_postal_code}</span><br>
</div>
<br>
<hr>
{jrCore_include template="footer.tpl"}