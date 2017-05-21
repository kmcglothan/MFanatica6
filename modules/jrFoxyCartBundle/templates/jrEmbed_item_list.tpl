{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}

{if isset($_items) && is_array($_items)}
    <div class="container">
        <table class="page_table">
            {foreach $_items as $key => $item}
                <tr class="{cycle values="page_table_row,page_table_row_alt"}">
                    <td class="page_table_cell" style="width:8%">{jrCore_module_function function="jrImage_stacked_image" module=$item.stacked_image_module type=$item.stacked_image_type item_id=$item.stacked_image_item_id size="40" alt=$item.bundle_title border_width=0}</td>
                    <td class="page_table_cell">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.bundle_title_url}" target="_blank"><h3>{$item.bundle_title}</h3></a>
                    </td>
                    <td class="page_table_cell center" style="width:16%">
                        <a onclick="jrEmbed_load_module('jrFoxyCartBundle', 1, 'profile_url:{$item.profile_url}');">@{$item.profile_name}</a>
                    </td>
                    <td class="page_table_cell center" style="width:16%">
                        {if is_array($item.bundle_items)}
                            {$item.bundle_items|count} {jrCore_lang module="jrFoxyCartBundle" id=29}
                        {/if}</td>
                    <td class="page_table_cell center" style="width:16%">
                        {if isset($item.bundle_item_price) && $item.bundle_item_price > 0}
                            {$item.bundle_item_price}
                        {else}
                            -
                        {/if}
                    </td>
                    <td class="page_table_cell" style="width:10%">
                        <input type="button" class="form_button embed_form_button" value="{jrCore_lang module="jrEmbed" id="1" default="Embed this Media"}" onclick="jrFoxyCartBundle_insert_bundle({$item._item_id})">
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>

{else}

    <div class="container">
        <table class="page_table">
            <tr class="page_table_row">
                <td class="page_table_cell center" colspan="2">{jrCore_lang module="jrFoxyCartBundle" id="45" default="no item bundles were found"}</td>
            </tr>
        </table>
    </div>

{/if}

<script type="text/javascript">
    function jrFoxyCartBundle_insert_bundle(bundle_id)
    {
        parent.tinymce.activeEditor.insertContent('[jrEmbed module="jrFoxyCartBundle" id="' + bundle_id + '"]');
        parent.tinymce.activeEditor.windowManager.close();
    }
</script>
