{if isset($_items)}
    {foreach from=$_items item="item"}
        {jrBeatSlinger_process_item item=$item module=$_conf.jrBeatSlinger_list_1_type assign="_item"}
        <div class="col2 index_item">
            <div class="wrap">
                <div style="position: relative;">

                    <a href="{$_item.url}">
                        {jrCore_module_function
                        function="jrImage_display"
                        module=$_item.module
                        type=$_item.image_type
                        item_id=$_item._item_id
                        size="xlarge"
                        crop="2:3"
                        class="img_scale"
                        alt=$_item.title
                        width=false
                        height=false
                        }</a>

                    <div class="hover">
                        <div class="middle">
                            <div class="wrap">
                                <span class="title">{$_item.title}</span>
                                {if $_item.module != 'jrProfile'}
                                    <span>by {$item.profile_name}</span><br>
                                {else}
                                    <span style="text-transform: capitalize;">{$item.quota_jrProfile_name}</span><br>
                                {/if}
                                <button onclick="jrCore_window_location('{$_item.url}')">{$_item.read_more}</button>
                            </div>
                        </div>
                        {if $_item.module == 'jrAudio'}
                            <span class="audio_button"><a id="audio_{$item._item_id}" href="#"></a>
                                <input type="hidden" id="title" value="{$item.audio_title|urlencode|truncate:50}" />
                                <input type="hidden" id="artist" value="{$item.profile_name|truncate:50|urlencode}" />
                                <input type="hidden" id="mp3" value="{$jamroom_url}/{$_item.murl}/stream/audio_file/{$item._item_id}/key=[jrCore_media_play_key]/file.mp3" />
                                <input type="hidden" id="oga" value="{$jamroom_url}/{$_item.murl}/stream/audio_file/{$item._item_id}/key=[jrCore_media_play_key]/file.ogg" />
                                <input type="hidden" id="poster" value="{$jamroom_url}/{$_item.murl}/image/audio_image/{$item._item_id}/large/crop=3:2" />
                                <input type="hidden" id="image" value="{$jamroom_url}/{$_item.murl}/image/audio_image/{$item._item_id}/24/crop=auto" />
                                <input type="hidden" id="id" value="{$item._item_id}" />
                                <input type="hidden" id="profile_id" value="{$item.profile_id}" />
                                <input type="hidden" id="album_url" value="{$jamroom_url}/{$item.profile_url}/{$_item.murl}/albums/{$item.audio_album_url}" />
                                <input type="hidden" id="item_url" value="{$jamroom_url}/{$item.profile_url}/{$_item.murl}/{$item._item_id}/{$item.audio_album_url}" />
                                <input type="hidden" id="key" value="[jrCore_media_play_key]" />
                                <input type="hidden" id="module" value="{$_params.module}" />
                                <input type="hidden" id="price" value="{$item.audio_file_item_price|default:"0"}" />
                                <input type="hidden" id="album" value="{$item.audio_album|default:"N/A"}" />
                                <input type="hidden" id="genre" value="{$item.audio_genre|default:""}" />
                                <input type="hidden" id="date" value="{$item._created|jrCore_date_format:"relative"}" />
                                <input type="hidden" id="field" value="audio_file" />
                                <input type="hidden" id="currency" value="{$_conf.jrFoxyCart_store_currency}" />
                                <input type="hidden" id="url" value="{$_item.murl}" />
                            </span>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{else}
    <div class="no-items">
        <h1>{jrCore_lang skin="jrBeatSlinger" id="156" default="No items found"}</h1>
        {jrCore_module_url module="jrCore" assign="core_url"}
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}/section=list+1')">{jrCore_lang skin="jrBeatSlinger" id="157" default="Edit Configuration"}</button>
    </div>
{/if}