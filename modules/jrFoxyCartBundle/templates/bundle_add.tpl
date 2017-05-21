{*this is the list of all the bundles the user currently has and a box to add a new one *}
<div id="bundle_message" style="display:none"></div>

<form id="new_bundle_form">
<table style="width:100%;">
{if is_array($_items)}
    {jrCore_module_url module="jrFoxyCartBundle" assign="murl"}
    {foreach $_items as $_b}
    <tr>
        <td class="bundle_name"><a href="{$jamroom_url}/{$_b.profile_url}/{$murl}/{$_b._item_id}/{$_b.bundle_title_url}">{$_b.bundle_title|truncate:30}</a></td>
        <td class="bundle_count">{$_b.bundle_count} items</td>
        <td class="bundle_price right" style="padding-right:12px;">{$_b.bundle_item_price|number_format:2} </td>
        <td>
            {* see if this item is already part of this bundle *}
            {if $_b.bundle_includes_item == '1'}
            <input type="button" class="form_button bundle_button form_button_disabled" value="included" style="margin:0 2px 5px 0;">
            {else}
            <input type="button" class="form_button bundle_button" value="{jrCore_lang module="jrFoxyCartBundle" id="28" default="add"}" onclick="jrFoxyCartBundle_inject('{$_b._item_id}','{$item_id}','{$field}','{$bundle_module}')" style="margin:0 2px 5px 0;">
            {/if}
        </td>
    </tr>
    {/foreach}

    {* page jumper *}
    {if $info.total_pages > 1}
        <tr>
            <td colspan="2">
                {if $info.this_page > 1}
                    <a href="" onclick="jrFoxyCartBundle_select('{$item_id}','{$field}','{$bundle_module}','{$info.prev_page}');return false">{jrCore_icon icon="arrow-left" size="16"}</a>
                {/if}
            </td>
            <td colspan="2" style="text-align:right;padding:3px;">
                {if $info.next_page > 0}
                    <a href="" onclick="jrFoxyCartBundle_select('{$item_id}','{$field}','{$bundle_module}','{$info.next_page}');return false">{jrCore_icon icon="arrow-right" size="16"}</a>
                {/if}
            </td>
        </tr>
    {/if}


{/if}

    <tr>
        <td colspan="2" style="width:80%;padding-top:12px;">
            <input id="new_bundle_{$item_id}" type="text" class="form_text" style="width:92%;" value="{jrCore_lang module="jrFoxyCartBundle" id="27" default="new bundle name"}" name="new_bundle" onfocus="if (this.value == '{jrCore_lang module="jrFoxyCartBundle" id="27" default="new bundle name"}'){ this.value = ''; }" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) {ldelim} $('#new_bundle_form').submit(function(event) { event.stopPropagation()}); {rdelim}">
        </td>
        <td style="width:15%;padding:12px 8px 0 0">
            <input id="bundle_price_{$item_id}" type="text" class="form_text" style="width:70px" value="{jrCore_lang module="jrFoxyCartBundle" id="3" default="price"}" name="new_bundle" onfocus="if (this.value == '{jrCore_lang module="jrFoxyCartBundle" id="3" default="price"}'){ this.value = ''; }" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) {ldelim} $('#new_bundle_form').submit(function(event) { event.stopPropagation()}); {rdelim}">
        </td>
        <td style="width:5%;padding-top:12px;">
            <input type="submit" value="{jrCore_lang module="jrFoxyCartBundle" id="30" default="create"}" class="form_button bundle_button" onclick="jrFoxyCartBundle_new('{$item_id}','{$field}','{$bundle_module}');return false;" style="margin:0 2px 0 0;">
        </td>
    </tr>

</table>
</form>

<div style="float:right;clear:both;margin:3px;padding-top:3px;">
    <img id="bundle_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="16" height="16" style="display:none" alt="{jrCore_lang module="jrCore" id="73" default="working..."}">&nbsp;
    <a id="bundle_close" href="" onclick="jrFoxyCartBundle_close();return false">{jrCore_icon icon="close" size="16"}</a>
</div>
