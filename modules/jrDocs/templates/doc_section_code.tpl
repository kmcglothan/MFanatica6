{jrCore_module_url module="jrDocs" assign="murl"}
<li data-id="{$_item_id}">
    {if strlen($doc_title_url) > 0}<a id="{$doc_title_url}"></a>{/if}
    <div id="c{$_item_id}">

        <div class="section_text">

            {if !empty($doc_title)}
                <div class="section_title">
                    <h2>{$doc_title}</h2>
                </div>
            {/if}

            {if jrProfile_is_profile_owner($_profile_id)}
                <script>$(function() { var mid = $('#m{$_item_id}'); $('#c{$_item_id}').hover(function() { mid.show(); }, function() { mid.hide(); } ); }); </script>
                <div id="m{$_item_id}" class="section_actions">
                    {jrCore_item_update_button module="jrDocs" action="`$murl`/section_update/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                    {jrCore_item_delete_button module="jrDocs" action="`$murl`/section_delete/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                </div>
            {/if}

            {$doc_content|jrCore_format_string:$profile_quota_id}<br>

        </div>

        <div class="section_code">
            <pre class="hljs">{$doc_code}</pre>
        </div>

    </div>

    {jrCore_include template="section_divider.tpl" module="jrDocs"}

</li>

