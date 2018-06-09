{jrCore_module_url module="jrFlickr" assign="murl"}

{if isset($_items)}
    {foreach from=$_items item="item"}
        {assign var="_data" value=$item.flickr_data|json_decode:true}
        {jrMogul_process_item item=$item module="jrFlick" assign="_item"}
        <div class="list_item">
           <div class="wrap">
               <div class="title">
                   <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.flickr_title_url}">
                       {$item.profile_name|truncate:55}
                   </a>
               </div>
               <div class="external_image">
                   <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.flickr_title_url}">
                   <img class="img_scale" src="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}.jpg" alt="{$item.flickr_title|jrCore_entity_string}"></a>
               </div>

               <div class="data clearfix">
                   <span>{$item.flickr_comment_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="109" default="Comments"}</span>
                   <span>{$item.flickr_like_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="110" default="Likes"}</span>
               </div>
           </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}