{jrCore_module_url module="jrDocs" assign="murl"}

{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrDocs" profile_url=$item.profile_url profile_name=$profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {if jrProfile_is_profile_owner($_profile_id)}
            <a id="new_section_button_0" title="{jrCore_lang module="jrCore" id="36" default="create"}" onclick="jrDocs_create_section('{$_profile_id}','{$_item_id}','0','end');return false">{jrCore_icon icon="pen"}</a>
            <div id="new_section_0" class="overlay new_section_box"><!-- new section drop down loads here --></div>
        {/if}
        {jrCore_item_detail_buttons module="jrDocs" item=$item}
    </div>
</div>

<div class="box">
    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrDocs" profile_url=$profile_url}

    <div class="box_body">
        <div class="wrap">
                    <div class="item_media" style="padding-bottom: 12px;">
                        <div class="wrap">

                        {* Show our table of contents if enabled *}
                        {if !empty($doc_table_of_contents)}
                            {$doc_table_of_contents}
                        {/if}

                        <section>
                            <ul class="sortable list" style="list-style:none outside none;padding-left:0;">
