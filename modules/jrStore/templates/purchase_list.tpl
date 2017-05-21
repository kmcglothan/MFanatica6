{jrCore_include template="header.tpl"}


{jrCore_module_url module="jrStore" assign="murl"}


{assign var="active_tab" value="list"}
{jrCore_include template="nav_tabs.tpl" module="jrStore"}

<h3>{jrCore_lang module="jrStore" id="78" default="Purchases list"}</h3>

<table>
    <tr>
        <th>{jrCore_lang module="jrStore" id="79" default="Price"}Price</th>
        <th>{jrCore_lang module="jrStore" id="80" default="Date"}</th>
        <th>{jrCore_lang module="jrStore" id="81" default="Seller"}</th>
        <th>{jrCore_lang module="jrStore" id="82" default="Details"}</th>
        <th>{jrCore_lang module="jrStore" id="83" default="Items"}</th>
        <th>{jrCore_lang module="jrStore" id="84" default="Status"}</th>
        <th>{jrCore_lang module="jrStore" id="85" default="Messages"}</th>
        <th>{jrCore_lang module="jrStore" id="86" default="Last Message From"}</th>
    </tr>
    {foreach $transactions as $_r}
        <tr>
            <td class="right">{$_r.sale_gross}</td>
            <td class="center">{$_r.purchase_created|date_format:"%d %b %Y"}</td>
            <td>{$_r['seller'].profile_name}</td>
            <td class="center"><a href="{$jamroom_url}/{$murl}/purchases/{$_r.purchase_txn_id}/{$_r['seller']._profile_id}" type="button" class="form_button p3">{$_r.purchase_txn_id}</a></td>
            <td class="right">{$_r.item_count}</td>
            <td class="center">{$_r.status_status}</td>
            <td class="center"><a href="{$jamroom_url}/{$murl}/purchases/{$_r.purchase_txn_id}/{$_r['seller']._profile_id}/communication" type="button" class="form_button p3">{$_r.message_count|default:1}</a></td>
            <td><a href="{$jamroom_url}/{$_r.message_last_user.profile_url}">{$_r.message_last_user.profile_name}</a> ({$_r.message_last_time|jrCore_date_format:"relative"})</td>
        </tr>
    {/foreach}
</table>

{jrCore_include template="footer.tpl"}