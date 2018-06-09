{*the button that displays 'add to bundle'*}
<div style="display: inline-block;" id="bundle_button_{$item_id}">
    {jrCore_lang module="jrBundle" id="25" default="add to bundle" assign="alt"}
    {$icon_html}
    <div id="bundle_{$item_id}" class="overlay bundle_box" style="display:none"><!-- bundle loads here --></div>
</div>