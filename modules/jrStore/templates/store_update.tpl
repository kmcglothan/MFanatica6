{jrCore_module_url module="jrStore" assign="murl"}
{if isset($_product_images) && is_array($_product_images)}
{foreach $_product_images as $img}

  <div class="jrstore_update_div">
    <a href="{$jamroom_url}/{$murl}/image/{$img}/{$_item_id}/xxxlarge" target="_blank" data-lightbox="images" alt="{$img}" title="{$img}">
        {jrCore_module_function function="jrImage_display" module="jrStore" type=$img item_id=$_item_id size="icon" class="jrstore_update_image" alt=$img width=false height=false}
    </a><br>
    <input type="button" class="jrstore_update_button" value="{jrCore_lang module="jrStore" id="28" default="delete"}" onclick="jrCore_window_location('{$jamroom_url}/{$murl}/delete_image/id={$_item_id}/field={$img}')">
  </div>

{/foreach}
{/if}