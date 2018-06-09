{jrCore_module_url module="jrEvent" assign="murl"}
{if isset($_items)}
    {foreach $_items as $item}
        <div class="list_item">
            <div class="wrap clearfix">
               <div class="row">
                   <div class="col4">
                       <div class="image">
                           <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">
                               {jrCore_module_function
                               function="jrImage_display"
                               module="jrEvent"
                               type="event_image"
                               item_id=$item._item_id
                               size="xxxlarge"
                               crop="auto"
                               class="img_scale"
                               alt=$item.event_title
                               width=false
                               height=false
                               }</a>
                       </div>
                   </div>
                   <div class="col8">
                       <div class="title">
                           <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}"> {$item.event_title|truncate:75}</a>
                       </div>
                       <span class="date">{$item.event_date|jrCore_date_format:"%A %B %e %Y, %l:%M %p"}</span><br>
                    <span>
                        {$item.event_description|strip_tags|truncate:250}
                    </span>
                       <div class="list_buttons">
                           {jrCore_item_list_buttons module="jrEvent" item=$item}
                       </div>

                       <div class="data clearfix">
                           <span>{$item.event_comment_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="109" default="Comments"}</span>
                           <span>{$item.event_like_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="110" default="Likes"}</span>
                       </div>
                   </div>
               </div>
            </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}


