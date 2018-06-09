{if isset($_items)}
    {jrCore_module_url module="jrVideo" assign="murl"}
    {$page = 0}
    {foreach from=$_items item="item"}
        {$class = ""}
        {if $item.list_rank == 6 || $item.list_rank == 7 || $item.list_rank == 8 || $item.list_rank == 9 || $item.list_rank == 15 || $item.list_rank == 16 || $item.list_rank == 17 || $item.list_rank == 18 || $item.list_rank == 24 || $item.list_rank == 25 || $item.list_rank == 26 || $item.list_rank == 27}
            {$class = " bottom_row"}
        {/if}


        {if $item.list_rank%9 == 1}
            {$page = $page+1}
            <div class="pane" id="page_{$page}">
            <div class="row">
            <div class="col4">
                <div class="image" id="item_{$item.list_rank}">
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
                        }
                        <div class="hover">
                            <div class="table">
                                <div class="table-row">
                                    <div class="table-cell">
                                        <h4>{$item.video_album|truncate:30}</h4>
                                        <span class="video_title">{$item.video_title|truncate:30}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
            </div>

        {else}
            <div class="col2" id="item_{$item.list_rank}">
                <div class="image{$class}">
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
                        }
                        <div class="hover">
                            <div class="table">
                                <div class="table-row">
                                    <div class="table-cell">
                                        <h4>{$item.video_album|truncate:30}</h4>
                                        <span class="video_title">{$item.video_title|truncate:30}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        {/if}


        {if $item.list_rank%9 == 0 || $item.list_rank == $info.total_items}
            </div>
            </div>
        {/if}
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}