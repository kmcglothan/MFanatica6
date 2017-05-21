{if isset($_items)}
    {jrCore_module_url module="jrVideo" assign="murl"}
    {$page = 0}
    {foreach from=$_items item="item"}
        {if $item.list_rank%6 == 1}
            {$page = $page+1}
            <div class="pane" id="page_{$page}">
        {/if}
        <div class="index_item" id="item_{$item.list_rank}">
            <div class="wrap">
                <div class="image">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">
                        {jrCore_module_function
                        function="jrImage_display"
                        module="jrVideo"
                        type="video_image"
                        item_id=$item._item_id
                        size="xxlarge"
                        crop="11:16"
                        class="img_scale"
                        alt=$item.video_title
                        width=false
                        height=false
                        }

                    <div class="hover">
                        <div class="table">
                            <div class="table-row">
                                <div class="table-cell">
                                    <h4>{$item.video_album|truncate:30}</h4>
                                    <span class="video_title">{$item.video_title|truncate:30}</span>
                                    <span>{$item.video_category|truncate:30}</span>
                                    <p>{$item.video_description|truncate:120}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
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