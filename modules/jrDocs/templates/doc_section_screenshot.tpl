<li data-id="{$_item_id}">
    {if strlen($doc_title_url) > 0}<a id="{$doc_title_url}"></a>{/if}
    <div>

        <div class="section_text">

            <div id="c{$_item_id}">
                {if !empty($doc_title)}
                <div class="section_title">
                    <h2>{$doc_title}</h2>
                </div>
                {/if}

                {jrCore_module_url module="jrDocs" assign="murl"}
                {if jrProfile_is_profile_owner($_profile_id)}
                    <script>$(function() { var mid = $('#m{$_item_id}'); $('#c{$_item_id}').hover(function() { mid.show(); }, function() { mid.hide(); } ); }); </script>
                    <div id="m{$_item_id}" class="section_actions">
                        {jrCore_item_update_button module="jrDocs" action="`$murl`/section_update/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                        {jrCore_item_delete_button module="jrDocs" action="`$murl`/section_delete/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                    </div>
                {/if}

                <div class="center" style="width:100%">
                    {if isset($doc_image_url) && strlen($doc_image_url) > 5}
                        <a href="{$doc_image_url}">{jrCore_module_function function="jrImage_display" module="jrDocs" type="doc_image" item_id=$_item_id size=1280 class="iloutline doc_screenshot_img img_scale" alt=$doc_title _v=$_updated}</a>
                    {else}
                        <a href="{$jamroom_url}/{$murl}/image/doc_image/{$_item_id}/1280/_v={$_updated}" data-lightbox="images" title="{$doc_title|jrCore_entity_string}">{jrCore_module_function function="jrImage_display" module="jrDocs" type="doc_image" item_id=$_item_id size="1280" class="iloutline doc_screenshot_img img_scale" alt=$doc_title _v=$_updated}</a>
                    {/if}

                    {if !empty($doc_content)}
                    <div class="section_caption">
                        {$doc_content|jrCore_format_string:$profile_quota_id}
                    </div>
                    {/if}

                </div>

            </div>

        </div>

    </div>

    {jrCore_include template="section_divider.tpl" module="jrDocs"}

</li>