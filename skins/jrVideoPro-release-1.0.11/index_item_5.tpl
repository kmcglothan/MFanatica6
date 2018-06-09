{if isset($_items)}
    {jrCore_module_url module="jrVideo" assign="murl"}
    {$page = 0}
    {foreach from=$_items item="item"}
        {if $item.list_rank%6 == 1}
            <div class="row">
        {/if}
        <div class="col2">
            <div class="index_item full" id="item_{$item.list_rank}">
                <div class="wrap">
                    <div class="image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">
                            {jrCore_module_function
                            function="jrImage_display"
                            module="jrVideo"
                            type="video_image"
                            item_id=$item._item_id
                            size="xxlarge"
                            crop="16:9"
                            class="img_scale"
                            alt=$item.video_title
                            width=false
                            height=false
                            }</a>
                    </div>
                    <div class="wrap">
                        <h4>
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{$item.video_album|truncate:40}</a>
                        </h4>
                        <span><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">{$item.video_title|truncate:30}</a></span>
                    </div>
                </div>
            </div>
        </div>
        {if $item.list_rank%6 == 0 || $item.list_rank == $info.total_items}
            </div>
        {/if}
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}