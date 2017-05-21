{jrCore_module_url module="jrStore" assign="murl"}
{jrCore_module_url module="jrFoxyCart" assign="murl_foxycart"}

<div class="block">
    <div class="title">
        <h1>{jrCore_lang module="jrStore" id="34" default="Product Sales"}</h1><br>

        <div class="breadcrumbs"><a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrStore" id="19" default="Products"}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}/sales">{jrCore_lang module="jrStore" id="34" default="Product Sales"}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}/sales/{$txn_id}">{$txn_id}</a> &raquo; {jrCore_lang module="jrStore" id="59" default="Communication"}</div>
    </div>

    {assign var="active_tab" value="communication"}
    {jrCore_include template="nav_tabs.tpl" module="jrStore"}

    {if $seller['_profile_id'] == $_user['_profile_id']}
        <div id="status_success" class="item success" style="display:none;">
            {jrCore_lang module="jrStore" id="87" default="Status was successfully updated"}
        </div>
        <label>{jrCore_lang module="jrStore" id="88" default="Order Status:"} <select name="status" id="status{$txn_id}" class="form_select" onchange="jrStoreStatus('{$txn_id}','{$seller['_profile_id']}');">
                <option value=""></option>
                <option value="onhold" {if $status_status == 'onhold'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="61" default="On Hold"}</option>
                <option value="processing" {if $status_status == 'processing'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="62" default="Processing"}</option>
                <option value="posted" {if $status_status == 'posted'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="63" default="Posted"}</option>
                <option value="delivered" {if $status_status == 'delivered'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="64" default="Delivered"}</option>
                <option value="canceled" {if $status_status == 'canceled'}selected="selected"{/if}>{jrCore_lang module="jrStore" id="65" default="Canceled"}</option>
            </select></label>
        <br>
    {/if}

    <h3>{jrCore_lang module="jrStore" id="123" default="Order Communication:"}</h3>
    <br>
    <br>
    <a id="comment_section" name="comment_section"></a>
    {jrCore_lang module="jrStore" id="124" default="Send a message:"}
    <br>

    <div class="item">
        <div id="comment_notice" style="display:none;"><!-- any comment errors load here --></div>
        <form id="cform" action="{$jamroom_url}/{$murl}/comment_save" method="POST" onsubmit="jrStoreComment('#cform','#comments');return false">
            <input type="hidden" id="comment_txn_id" name="comment_txn_id" value="{$txn_id}">
            <input type="hidden" id="comment_seller_profile_id" name="comment_seller_profile_id" value="{$seller['_profile_id']}">
            <textarea id="comment_text" cols="40" rows="5" class="form_textarea" name="comment_text"></textarea>
            <br>

            <div style="vertical-align:middle">
                <img id="form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}" style="margin:8px 8px 0px 8px;">
                <input id="comment_submit" type="submit" value="{jrCore_lang module="jrStore" id="16" default="Send"}" class="form_button" style="margin-top:8px;">
            </div>
        </form>
    </div>
    <h3>{jrCore_lang module="jrStore" id="89" default="Comments:"}</h3>

    <div id="comment_success" class="item success" style="display:none;">
        {jrCore_lang module="jrStore" id="15" default="Your comment was successfully posted"}
    </div>
    <div id="comments">
        {jrStore_list mode="comments" txn_id=$txn_id seller_profile_id=$seller['_profile_id']}
    </div>
</div>

