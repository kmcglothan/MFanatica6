{if isset($_items)}
    {foreach from=$_items item="item"}
        {jrMogul_process_item item=$item module="jrGallery" assign="_item"}
        <div class="list_item">
           <div class="wrap">
               <div class="title">
                   <a href="{$_item.url}">
                       {$_item.title|truncate:55}
                   </a>
               </div>
               <a href="{$_item.url}">
                   {jrCore_module_function
                   function="jrImage_display"
                   module=$_item.module
                   type=$_item.image_type
                   item_id=$_item._item_id
                   size="xlarge"
                   crop="2:1"
                   class="img_scale"
                   alt=$_item.title
                   width=false
                   height=false
                   }</a>

               <div class="data clearfix">
                   <span>{$item.gallery_comment_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="109" default="Comments"}</span>
                   <span>{$item.gallery_like_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="110" default="Likes"}</span>
               </div>
           </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}