{* Standard HTML search form *}

{jrCore_lang module="jrSearch" id="7" default="Search" assign="st"}

{assign var="form_name" value="jrSearch"}
<div style="white-space:nowrap">
    <form action="{$jamroom_url}/search/results/{$jrSearch.module}/{$jrSearch.page}/{$jrSearch.pagebreak}" method="{$jrSearch.method}" style="margin-bottom:0">
    <input id="search_input" type="text" name="search_string" style="{$jrSearch.style}" class="{$jrSearch.class}" placeholder="{$jrSearch.value|jrCore_entity_string}" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) { $(this).closest('form').submit(); }">&nbsp;<input type="submit" class="form_button" value="{$st}">
    </form>
</div>
