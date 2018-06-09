{jrCore_module_url module="jrVideo" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}

        <div class="list_item">
            <div class="wrap clearfix">
                <div class="col4">
                    <div class="image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.video_title width=false height=false}</a>
                    </div>
                </div>
                <div class="col8">
                    <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{$item.video_album}</a></span>
                    <span class="date">{$item._created|jrCore_date_format:"relative"}</span><br>
                    {if isset({$item.video_category}) && strlen({$item.video_category}) > 0}
                        <span class="date">{$item.video_category}</span><br>
                    {/if}
                    {if isset($item.video_description) && strlen($item.video_description) > 0}
                        <span class="date">{$item.video_description|truncate:200}</span>
                    {/if}
                    <div class="list_buttons">
                        {jrCore_item_list_buttons module="jrVideo" field="video_file" item=$item}
                    </div>
                    <div class="data clearfix">
                        <span>{$item.video_comment_count|jrCore_number_format} {jrCore_lang skin="jrBeatSlinger" id="109" default="Comments"}</span>
                        <span>{$item.video_like_count|jrCore_number_format} {jrCore_lang skin="jrBeatSlinger" id="110" default="Likes"}</span>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
{/if}