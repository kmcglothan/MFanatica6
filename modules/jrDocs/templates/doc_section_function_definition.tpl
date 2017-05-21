<li data-id="{$_item_id}">

    {if strlen($doc_title_url) > 0}<a id="{$doc_title_url}"></a>{/if}
    <div id="c{$_item_id}">

        <div class="section_text">

            {if !empty($doc_title)}
                <div class="section_title section_function_name">
                    <h2>{$doc_title}</h2>
                    <div style="float:right"><i>{jrCore_lang module="jrDocs" id="52" default="function"}</i></div>
                </div>

                <div class="section_title section_function_title">
                    {$doc_function_declaration}
                </div>
            {/if}

            {* function table *}
            {if !empty($doc_parameters)}
                <div class="section_function">
                    <div style="display:table-row">
                        <div class="section_header_cell" style="width:17%">{jrCore_lang module="jrDocs" id="45" default="parameter"}</div>
                        <div class="section_header_cell" style="width:10%">{jrCore_lang module="jrDocs" id="46" default="type"}</div>
                        <div class="section_header_cell" style="width:12%">{jrCore_lang module="jrDocs" id="47" default="default"}</div>
                        <div class="section_header_cell" style="width:5%">{jrCore_lang module="jrDocs" id="48" default="required"}</div>
                        <div class="section_header_cell" style="width:56%">{jrCore_lang module="jrDocs" id="49" default="description"}</div>
                    </div>
                    {foreach $doc_parameters as $_prm}
                        <div style="display:table-row">
                            <div class="section_function_cell">{$_prm.name}</div>
                            <div class="section_function_cell">{$_prm.type}</div>
                            <div class="section_function_cell">{$_prm.default|default:"-"}</div>
                            <div class="section_function_cell">{$_prm.required}</div>
                            <div class="section_function_cell">{$_prm.description}</div>
                        </div>
                    {/foreach}
                </div>
            {/if}

            <div class="section_function_desc">
                {$doc_content|jrCore_format_string:$profile_quota_id}
            </div>

            {if jrProfile_is_profile_owner($_profile_id)}
                <script>$(function() { var mid = $('#m{$_item_id}'); $('#c{$_item_id}').hover(function() { mid.show(); }, function() { mid.hide(); } ); }); </script>
                {jrCore_module_url module="jrDocs" assign="murl"}
                <div id="m{$_item_id}" class="section_actions">
                    {jrCore_item_update_button module="jrDocs" action="`$murl`/section_update/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                    {jrCore_item_delete_button module="jrDocs" action="`$murl`/section_delete/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                </div>
            {/if}

        </div>

    </div>

    {jrCore_include template="section_divider.tpl" module="jrDocs"}

</li>