{* Standard HTML rating form *}
{assign var="form_name" value="RF`$jrRating.module_url``$jrRating.item_id``$jrRating.index`"}
{if $jrRating.norate == 0}
    <form name="{$form_name}">
    <select style="{$jrRating.style}" class="{$jrRating.class}" name="rating_value" onChange="jrRating_rate_item('{$jrRating.html_id}', {$form_name}.target.value, {$form_name}.module_url.value, {$form_name}.item_id.value, {$form_name}.rating_value.value, {$form_name}.index.value);">
    <option value="-">{$jrRating.values.0}</option>
    <option value="1">{$jrRating.values.1}</option>
    <option value="2">{$jrRating.values.2}</option>
    <option value="3">{$jrRating.values.3}</option>
    <option value="4">{$jrRating.values.4}</option>
    <option value="5">{$jrRating.values.5}</option>
    </select>
    <input type="hidden" name="target" value="{$jrRating.target}">
    <input type="hidden" name="module_url" value="{$jrRating.module_url}">
    <input type="hidden" name="item_id" value="{$jrRating.item_id}">
    <input type="hidden" name="index" value="{$jrRating.index}">
    </form>
{/if}