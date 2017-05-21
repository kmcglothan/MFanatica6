<li data-id="{$_item_id}">
    {if strlen($doc_title_url) > 0}<a id="{$doc_title_url}"></a>{/if}
    <div id="c{$_item_id}">

        {if jrProfile_is_profile_owner($_profile_id)}
            <script>$(function() { var mid = $('#m{$_item_id}'); $('#c{$_item_id}').hover(function() { mid.show(); }, function() { mid.hide(); } ); }); </script>
            {jrCore_module_url module="jrDocs" assign="murl"}
            <div style="position:relative">
                <div id="m{$_item_id}" class="section_actions" style="top:6px;right:16px">
                    {jrCore_item_update_button module="jrDocs" action="`$murl`/section_update/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                    {jrCore_item_delete_button module="jrDocs" action="`$murl`/section_delete/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                </div>
            </div>
        {/if}

        <div class="section_hint">
            {$doc_content|jrCore_format_string:$profile_quota_id}
        </div>

    </div>

    {jrCore_include template="section_divider.tpl" module="jrDocs"}

</li>