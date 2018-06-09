{jrCore_module_url module="jrGallery" assign="murl"}

{if isset($_items)}
    {foreach from=$_items item="item"}
        {jrISkin_process_item item=$item module="jrGallery" assign="_item"}
        <div class="list_item">
           <div class="wrap">
               <div class="title">
                   <a href="{$_item.url}">
                      {$item.gallery_title|truncate:55}
                   </a>
               </div>
               <a href="{$_item.url}">
                   {jrCore_module_function
                   function="jrImage_display"
                   module="jrGAllery"
                   type="gallery_image"
                   item_id=$item._item_id
                   size="xlarge"
                   crop="2:1"
                   class="img_scale"
                   alt=$item.gallery_title
                   width=false
                   height=false
                   }</a>

               <div class="data clearfix">
                   <span>{$item.gallery_comment_count|jrCore_number_format} {jrCore_lang skin="jrISkin" id="109" default="Comments"}</span>
                   <span>{$item.gallery_like_count|jrCore_number_format} {jrCore_lang skin="jrISkin" id="110" default="Likes"}</span>
               </div>
           </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}