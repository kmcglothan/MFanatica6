{if isset($_items)}

    {foreach from=$_items item="item"}
        <div class="list_item">
            <div class="wrap clearfix">


                {if strlen($item.audio_title) > 0}
                    {jrCore_module_url module="jrAudio" assign="murl"}
                    <div class="col3">
                        <div class="image">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.audio_title width=false height=false}</a>
                        </div>
                    </div>
                    <div class="col9 last">
                        {if $item.audio_active == 'on' && $item.audio_file_extension == 'mp3'}
                            {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                        {/if}
                        <span class="title"><a
                                    href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title|truncate:40}</a></span>
                        <span class="date"><a
                                    href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album|truncate:40}</a></span>
                        <span class="date"><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></span>
                    </div>
                {else}
                    {jrCore_module_url module="jrSoundCloud" assign="murl"}
                    <div class="col3">
                        <div class="image">
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}"><img src="{$item.soundcloud_artwork_url}" alt="{$item.soundcloud_title|jrCore_entity_string}" class="img_scale" /></a>
                        </div>
                    </div>
                    <div class="col9 last">
                        {jrSoundCloud_player params=$item}
                        <span class="title"><a
                                    href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">{$item.soundcloud_title|truncate:40}</a></span>
                        <span class="date">{$item.soundcloud_artist|truncate:40}</span>
                        <span class="date"><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></span>
                    </div>
                {/if}


            </div>
        </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}