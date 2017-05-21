{jrCore_module_url module="jrDocs" assign="murl"}
<div class="block">

    <div class="title">
        <div style="float:right;" class="section_header_buttons">

            {if jrProfile_is_profile_owner($_profile_id)}
            <a id="new_section_button_0" title="{jrCore_lang module="jrCore" id="36" default="create"}" onclick="jrDocs_create_section('{$_profile_id}','{$_item_id}','0','end');return false">{jrCore_icon icon="pen"}</a>
            <div id="new_section_0" class="overlay new_section_box"><!-- new section drop down loads here --></div>
            {/if}
            {jrCore_item_detail_buttons module="jrDocs" item=$item}

        </div>
        <h1>{$doc_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrDocs" id="53" default="Documentation"}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$doc_category_url}">{$doc_category}</a> &raquo; {$doc_title}
        </div>
    </div>

    <div class="block_content">

        <div class="item">

        {* Show our table of contents if enabled *}
        {if !empty($doc_table_of_contents)}
            {$doc_table_of_contents}
        {/if}

        <section>
            <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
