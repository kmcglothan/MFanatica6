{jrCore_module_url module="jrStore" assign="murl"}

<div class="block_content" style="font-size: 0.7em">
    <div class="title">
        <h1>{jrCore_lang module="jrStore" id="34" default="Product Sales"}</h1><br>

        <div class="breadcrumbs"><a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrStore" id="19" default="Products"}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}/sales">{jrCore_lang module="jrStore" id="34" default="Product Sales"}</a></div>
    </div>

    {assign var="active_tab" value="list"}
    {jrCore_include template="nav_tabs.tpl" module="jrStore"}

    {*for the sortable tables in the loaded div*}
    <style type="text/css" title="currentStyle">
        @import "{$jamroom_url}/modules/jrStore/contrib/DataTables-1.9.4/smoothness/jquery-ui-1.8.4.custom.css";
    </style>
    <link rel="stylesheet" href="{$jamroom_url}/modules/jrStore/contrib/DataTables-1.9.4/media/css/jquery.dataTables.css" media="screen"/>
    <script type="text/javascript" src="{$jamroom_url}/modules/jrStore/contrib/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>

    <table id="transactions">
        <thead>
        <tr>
            <th>{jrCore_lang module="jrStore" id="116" default="Purchaser"}</th>
            <th>{jrCore_lang module="jrStore" id="70" default="Name"}</th>
            <th>{jrCore_lang module="jrStore" id="117" default="Gross"}</th>
            <th>{jrCore_lang module="jrStore" id="118" default="Fees"}</th>
            <th>{jrCore_lang module="jrStore" id="119" default="Net"}</th>
            <th title="Shipping">{jrCore_lang module="jrStore" id="120" default="extra"}</th>
            <th>{jrCore_lang module="jrStore" id="58" default="Details"}</th>
            <th>{jrCore_lang module="jrStore" id="80" default="Date"}</th>
            <th>{jrCore_lang module="jrStore" id="84" default="Status"}</th>
            <th>{jrCore_lang module="jrStore" id="121" default="Notes"}</th>
            <th>{jrCore_lang module="jrStore" id="122" default="Last"}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $transactions as $_r}
            <tr>
                <td>{$_r['buyer'].profile_name}</td>
                <td>{$_r.buyer_firstname} {$_r.buyer_lastname}</td>
                <td>{$_r.sale_gross}</td>
                <td><i>{$_r.sale_system_fee}</i></td>
                <td><b>{$_r.sale_total_net}</b></td>
                <td>{$_r.sale_shipping}</td>
                <td><a href="{$jamroom_url}/{$profile_url}/{$murl}/sales/{$_r.purchase_txn_id}" type="button" class="form_button p3">{$_r.purchase_txn_id}</a></td>
                <td>{$_r.purchase_created|date_format:"%Y-%m-%d"}</td>
                <td>{$_r.status_status}</td>
                <td><a href="{$jamroom_url}/{$profile_url}/{$murl}/sales/{$_r.purchase_txn_id}/communication" type="button" class="form_button p3">{$_r.message_count|default:1}</a></td>
                <td><a href="{$jamroom_url}/{$_r.message_last_user.profile_url}">{$_r.message_last_user.profile_name}</a> ({$_r.message_last_time|jrCore_date_format:"relative"})</td>



            </tr>
        {/foreach}
        </tbody>
    </table>


    <script type="text/javascript">
        $(document).ready(function () {
            $('#transactions').dataTable({
                "bJQueryUI": true,
                "sPaginationType": "full_numbers",
                /* Disable initial sort */
                "aaSorting": []
            });
        });
    </script>
</div>