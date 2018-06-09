{jrCore_module_url module="jrAudio" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}
    <div class="list_item">
       <div class="wrap clearfix">
           <div class="col3">
               <div class="image">
                   <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.audio_title width=false height=false}</a>
               </div>
           </div>

           <div class="col9 last">
               <div style="float: right;">
                   {jrPlaylist_button playlist_for="jrAudio" item_id=$item._item_id}
               </div>
               <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></span>
               <span class="date"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album|truncate:40}</a></span>
               <span class="date"><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></span>

           </div>

       </div>
    </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}