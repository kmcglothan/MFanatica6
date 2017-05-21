{if jrProfile_is_profile_owner($_profile_id)}
    <div id="b{$_item_id}" class="sprite_icon new_section_button">
        <a id="new_section_button_{$_item_id}" onclick="jrDocs_create_section('{$_profile_id}','{$doc_group_id}','{$_item_id}','{$doc_section_order}')">{jrCore_lang module="jrDocs" id="59" default="add new section"}</a>
    </div>
    <div id="new_section_{$_item_id}" class="overlay new_section_box"><!-- new section drop down loads here --></div>
    <div style="clear:both"></div>
{/if}