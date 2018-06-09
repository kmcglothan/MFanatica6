
{if isset($_items)}
    {$rank = 0}
    {foreach from=$_items item="item"}
        {$class = ''}
        {$rank = $rank+1}
        {if $rank%2 == 0}
            {$class = ' odd'}
        {/if}
        <div class="list_item{$class}">
            <div class="table">
                <div class="table-row">
                    {if strlen($item.audio_title) > 0}
                        {jrCore_module_url module="jrAudio" assign="murl"}
                        <div class="table-cell" style="width: 30px; text-align: center">
                            {$item.chart_position}
                        </div>
                        <div class="table-cell" style="width: 28px; text-align: center;">
                            {$color = '777777'}
                            {$icon = "chart_same"}

                            {if $item.chart_new_entry == 'yes'}
                                {$color = '339933'}
                                {$icon = 'chart_up'}
                            {elseif $item.chart_direction == 'same'}
                                {$icon = "chart_same"}
                            {elseif $item.chart_direction == 'up'}
                                {$icon = 'chart_up'}
                                {if $item.chart_change > 5}
                                    {$color = 'FF5500'}
                                {/if}
                            {else}
                                {$icon = 'chart_down'}
                                {if $item.chart_change > 5}
                                    {$color = '3393ff'}
                                {/if}
                            {/if}

                            {jrCore_icon icon=$icon size="24" color=$color title='hi'}
                        </div>
                        <div class="table-cell" style="width: 30px; text-align: center">

                            {if $item.chart_new_entry == 'yes'}
                                &mdash;
                            {elseif $item.chart_direction == 'same'}
                                {$item.chart_position}
                            {elseif $item.chart_direction == 'up'}
                                {$item.chart_position + $item.chart_change}
                            {else}
                                {$item.chart_position - $item.chart_change}
                            {/if}

                        </div>
                        <div class="table-cell desk" style="width: 30px; text-align: center">
                            <div class="image">
                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">
                                    {jrCore_module_function
                                    function="jrImage_display"
                                    module="jrAudio"
                                    type="audio_image"
                                    item_id=$item._item_id
                                    size="xlarge"
                                    crop="auto"
                                    class="img_scale"
                                    alt=$item.audio_title
                                    width=false
                                    height=false
                                    }</a>
                            </div>
                        </div>
                        <div class="table-cell" style="width: 22px">
                            {if $item.audio_active == 'on' && $item.audio_file_extension == 'mp3'}
                                {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                            {else}
                                &nbsp;
                            {/if}
                        </div>
                        <div class="table-cell">
                                <span class="index_title"><a
                                            href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title|truncate:40}</a></span>
                        </div>
                        <div class="table-cell desk" style="width:200px;">
                                <span class="date"><a
                                            href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name|truncate:24}</a></span>
                        </div>
                        <div class="table-cell desk" style="width: 100px">
                                <span class="date"><a
                                            href="{$jamroom_url}/{$item.profile_url}">{$item.audio_genre}</a></span>
                        </div>
                        <div class="table-cell desk" style="width: 60px; text-align: right">
                            <span class="date">{$item.chart_count|jrCore_number_format}</span>
                        </div>
                        <div class="table-cell chart_buttons" style="width: 130px">
                            {jrLike_button item=$item module="jrAudio" action="like"}
                            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
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
        {if $_conf.jrAudioPro_require_price_3 == 'on'}
            {jrCore_lang skin="jrAudioPro" id="63" default="This list currently requires items to have a price set."}
        {/if}
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}/section=List+2')">{jrCore_lang skin="jrAudioPro" id="64" default="Edit Configuration"}</button>
    </div>
{/if}