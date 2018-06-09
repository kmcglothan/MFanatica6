{jrCore_module_url module="jrProduct" assign="murl"}

{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="list_item">
           <div class="wrap">
               <div class="title">
                   <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.product_title_url}">
                       {$item.product_title|truncate:55}
                   </a>
               </div>
               <div class="image">
                   <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.product_title_url}">
                       {jrCore_module_function
                       function="jrImage_display"
                       module="jrProduct"
                       type="product_image"
                       item_id=$item._item_id
                       size="xlarge"
                       crop="2:1"
                       class="img_scale"
                       alt=$item.product_title
                       width=false
                       height=false
                       }
                   </a>
               </div>

               <div class="data clearfix">
                   <span>{$item.product_comment_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="109" default="Comments"}</span>
                   <span>{$item.product_like_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="110" default="Likes"}</span>
               </div>
           </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}