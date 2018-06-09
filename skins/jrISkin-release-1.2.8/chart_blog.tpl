{jrCore_module_url module="jrBlog" assign="murl"}

{if isset($_items)}
    {foreach from=$_items item="item"}

        <div class="list_item">
           <div class="wrap">
               <div class="title">
                   <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">
                       {$item.blog_title|truncate:55}
                   </a>
               </div>
               <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">
                   {jrCore_module_function
                   function="jrImage_display"
                   module="jrBlog"
                   type="blog_image"
                   item_id=$item._item_id
                   size="xlarge"
                   crop="2:1"
                   class="img_scale"
                   alt=$item.blog_title
                   width=false
                   height=false
                   }</a>

               <div class="data clearfix">
                   <span>{$item.blog_comment_count|jrCore_number_format} {jrCore_lang skin="jrISkin" id="109" default="Comments"}</span>
                   <span>{$item.blog_like_count|jrCore_number_format} {jrCore_lang skin="jrISkin" id="110" default="Likes"}</span>
               </div>
           </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}