{jrCore_module_url module="jrAudio" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="list_item">
        <div class="wrap clearfix">
            <div class="row">
                <div class="col4">
                    <div class="image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.audio_title width=false height=false}</a>
                    </div>
                </div>
                <div class="col8">
                    {if $item.audio_active == 'on' && $item.audio_file_extension == 'mp3'}
                        {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                    {else}
                        &nbsp;
                    {/if}

                    <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></span>
                    <span class="date"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a></span>
                    <span class="date">{$item.audio_genre}</span><br>
                    <span>{$item.audio_text|truncate:200}</span>
                    <div class="list_buttons">
                        {jrCore_item_list_buttons module="jrAudio" field="audio_file" item=$item}
                    </div>
                    <div class="data clearfix">
                        <span>{$item.audio_comment_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="109" default="Comments"}</span>
                        <span>{$item.audio_like_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="110" default="Likes"}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}