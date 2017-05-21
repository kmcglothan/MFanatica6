{jrCore_module_url module="jrStore" assign="murl"}

<div class="block">
    <div class="title">
        <h1>{jrCore_lang module="jrStore" id="34" default="Product Sales"}</h1><br>

        <div class="breadcrumbs"><a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrStore" id="19" default="Products"}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}/sales">{jrCore_lang module="jrStore" id="34" default="Product Sales"}</a> &raquo; {$txn_id} </div>
    </div>

    {assign var="active_tab" value="details"}
    {jrCore_include template="nav_tabs.tpl" module="jrStore"}

    <div id="status_success" class="item success" style="display:none;">
        {jrCore_lang module="jrStore" id="87" default="Status was successfully updated"}
    </div>

    <br>
    {jrCore_lang module="jrStore" id="88" default="Order Status:"}
    <select name="status" id="status{$txn_id}" class="form_select" onchange="jrStoreStatus('{$txn_id}', '{$seller['_profile_id']}');">
        <option value=""></option>
        <option value="onhold" {if $status_status == 'onhold'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="61" default="On Hold"}</option>
        <option value="processing" {if $status_status == 'processing'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="62" default="Processing"}</option>
        <option value="posted" {if $status_status == 'posted'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="63" default="Posted"}</option>
        <option value="delivered" {if $status_status == 'delivered'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="64" default="Delivered"}</option>
        <option value="canceled" {if $status_status == 'canceled'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="65" default="Canceled"}</option>
    </select>
    <br>
    <br>

    <h3>{jrCore_lang module="jrStore" id="125" default="Order Details:"} {$txn_id}</h3>
    <table>
        <tr valign="top">
            <td>

                <table style="border: 1px solid #000000; width: 95%;">
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="90" default="Customer First Name"}</td>
                        <td>{$txn_customer_first_name}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="91" default="Customer Last Name"}</td>
                        <td>{$txn_customer_last_name}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="92" default=""}</td>
                        <td>{$txn_customer_id}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="93" default="Customer Postal Code"}</td>
                        <td>{$txn_customer_postal_code}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="94" default="Purchase Date"}</td>
                        <td>{$txn_date}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="95" default="Email"}</td>
                        <td>{$txn_customer_email}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="96" default="Company"}</td>
                        <td>{$txn_customer_company}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="97" default="Address1"}</td>
                        <td>{$txn_customer_address1}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="98" default="Address2"}</td>
                        <td>{$txn_customer_address2}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="99" default="City"}</td>
                        <td>{$txn_customer_city}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="100" default="Country"}</td>
                        <td>{$txn_customer_country}</td>
                    </tr>
                </table>
            </td>
            <td>

                <table style="border: 1px solid #000000; width: 95%;">
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="101" default="Order Total"}</td>
                        <td>{$txn_order_total}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="102" default="Product Total"}</td>
                        <td>{$txn_product_total}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="103" default="Shipping First Name"}</td>
                        <td>{$txn_shipping_first_name}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="104" default="Shipping Last Name"}</td>
                        <td>{$txn_shipping_last_name}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="105" default="Ship to Company Name"}</td>
                        <td>{$txn_shipping_company}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="106" default="Shipping Address1"}</td>
                        <td>{$txn_shipping_address1}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="107" default="Shipping Address2"}</td>
                        <td>{$txn_shipping_address2}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="108" default="Shipping Country"}</td>
                        <td>{$txn_shipping_city}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="109" default="Country"}</td>
                        <td>{$txn_shipping_country}</td>
                    </tr>

                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="110" default="Shipping postal Code"}</td>
                        <td>{$txn_shipping_postal_code}</td>
                    </tr>
                    <tr class="page_table_row_alt">
                        <td>{jrCore_lang module="jrStore" id="111" default="Shipping Total"}</td>
                        <td>{$txn_shipping_total}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="112" default="Shipping Tax Total"}</td>
                        <td>{$txn_tax_total}</td>
                    </tr>
                    <tr class="page_table_row">
                        <td>{jrCore_lang module="jrStore" id="113" default="%1 Fees" 1=$_conf.jrCore_system_name}</td>
                        <td>{$txn_sale_system_fee}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <br>

    <h3>{jrCore_lang module="jrStore" id="114" default="Order Items"}</h3><br>
    <table style="border: 1px solid #000000;">
        <tr>
            <th class="left">{jrCore_lang module="jrStore" id="70" default="Name"}</th>
            <th class="right">{jrCore_lang module="jrStore" id="71" default="Qty"}</th>
        </tr>
        {foreach $sold_items as $_s}
            <tr>
                <td><a href="{$jamroom_url}/{$_s['details'].profile_url}/{$murl}/{$_s['details']._item_id}/{$_s['details'].product_title_url}">{$_s['details'].product_title}</a></td>
                <td class="right">{$_s.purchase_qty}</td>
            </tr>
        {/foreach}
    </table>
    <br>
    <br>

    <h3>{jrCore_lang module="jrStore" id="115" default="Shipping Label"}</h3><br>

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

</div>