{if isset($_items)}
    {foreach from=$_items item="item"}
        {jrMSkin_process_item item=$item module='jrAudio' assign="_item"}
        <div class="col6">
            <div class="index_row">
                <div class="wrap">
                    <div style="display: table; width: 100%">
                        <div style="display: table-row">
                            <div style="display: table-cell; width: 30%;">
                                <a href="{$_item.url}">
                                    {jrCore_module_function
                                    function="jrImage_display"
                                    module=$_item.module
                                    type=$_item.image_type
                                    item_id=$_item._item_id
                                    size="xxxlarge"
                                    crop="3:2"
                                    class="img_scale"
                                    alt=$_item.title
                                    width=false
                                    height=false
                                    }</a>
                            </div>
                            <div style="display: table-cell; width: 67%;">
                                {$_item.title|truncate:24}<br>
                                <span>by {$item.profile_name|truncate:24}</span>
                            </div>
                            <div style="display: table-cell; width: 3%">
                                {if $item.audio_active == 'on' && $item.audio_file_extension == 'mp3'}
                                    {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                                {else}
                                    <button class="form_button">{jrCore_lang skin="jrMSkin" id=71 default="Read More"}</button>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{else}
    <div class="no-items" style="width: 100%;">
        <h1>{jrCore_lang skin="jrMSkin" id="147" default="No items found"}</h1>
        {jrCore_module_url module="jrCore" assign="core_url"}
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}/section=list+3')">{jrCore_lang skin="jrMSkin" id="148" default="Edit Configuration"}</button>
    </div>
{/if}