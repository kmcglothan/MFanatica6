
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="list_item">
            <div class="table">
                <div class="table-row">
                    {if strlen($item.audio_title) > 0}
                        {jrCore_module_url module="jrAudio" assign="murl"}
                        <div class="table-cell" style="width:10%;">
                            {if $item.audio_active == 'on' && $item.audio_file_extension == 'mp3'}
                                {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                            {else}
                                &nbsp;
                            {/if}
                        </div>
                        <div class="table-cell" style="width: 50%">
                            <span class="index_title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title|truncate:20}</a></span>
                            <span class="date"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a></span>
                        </div>
                        <div class="table-cell">
                            {jrLike_button item=$item module="jrAudio" action="like"}
                            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                        </div>
                    {else}
                        {jrCore_module_url module="jrSoundCloud" assign="murl"}
                        <div class="table-cell" style="width:10%;">
                            {jrSoundCloud_player params=$item}
                        </div>
                        <div class="table-cell" style="width: 66%">
                            <span class="index_title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">{$item.soundcloud_title|truncate:24}</a></span>
                            <span class="date"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">{$item.soundcloud_artist}</a></span>
                        </div>
                        <div class="table-cell">
                            {jrLike_button item=$item module="jrSoundCloud" action="like"}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    {/foreach}
{else}
    <div class="no-items">
        <h1>{jrCore_lang skin="jrAudioPro" id="62" default="No items found"}</h1>
        {jrCore_module_url module="jrCore" assign="core_url"}
        {if $_conf.jrAudioPro_require_price_2 == 'on'}
            {jrCore_lang skin="jrAudioPro" id="63" default="This list currently requires items to have a price set."}
        {/if}
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}/section=List+2')">{jrCore_lang skin="jrAudioPro" id="64" default="Edit Configuration"}</button>
    </div>
{/if}