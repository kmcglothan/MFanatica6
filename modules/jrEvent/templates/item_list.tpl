{jrCore_module_url module="jrEvent" assign="murl"}
{if isset($_items)}
    {foreach $_items as $item}
    <div class="item">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="large" crop="auto" alt=$item.event_title width=false height=false class="iloutline img_scale"}</a><br>
                    </div>
                </div>
                <div class="col7">
                    <div class="p5" style="padding-left:12px;">

                        <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a></h2><br>

                        <span class="normal">{$item.event_date|jrCore_date_format:"%A %b %e %Y, %l:%M %p":false}{if $item.event_end_day} {jrCore_lang module="jrEvent" id=64 default="-"} {$item.event_end_day|jrCore_date_format:"%A %b %e %Y, %l:%M %p":false}{/if}</span>
                        {if isset($item.event_location) && strlen($item.event_location) > 0}
                            <br><span class="normal">@ {$item.event_location|jrCore_strip_html|truncate:60}</span>
                        {/if}

                        {if isset($item.event_description) && strlen($item.event_description) > 0}
                            <br><span class="normal">{$item.event_description|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:180}</span>
                        {/if}

                        <br>{jrCore_module_function function="jrRating_form" type="star" module="jrEvent" index="1" item_id=$item._item_id current=$item.event_rating_1_average_count|default:0 votes=$item.event_rating_1_count|default:0}

                    </div>
                </div>
                <div class="col3 last">
                    <div class="block_config">
                        {jrCore_item_list_buttons module="jrEvent" item=$item}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

        </div>

    </div>
    {/foreach}
{/if}
