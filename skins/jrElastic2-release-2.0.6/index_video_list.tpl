{jrCore_module_url module="jrVideo" assign="murl"}
{foreach $_items as $item}
    {$row = 6}
    {$class = "col2"}
    {if jrCore_is_tablet_device()}
        {$row = 4}
        {$class = "col3"}
    {/if}

    {if $item.list_rank%$row == 1}
        <div class="row">
    {/if}
    <div class="{$class}">

        <div class="p10">
            <div class="image">
                {jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="large" class="img_scale" crop="16:9" alt=$item.video_title}

                <div class="hover">
                    <div class="table">
                        <div class="table-row">
                            <div class="table-cell">
                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">{jrCore_lang skin="jrElastic2" id=71 default="Watch Video" assign="title"}
                                    {jrCore_icon icon="video" size="40" color="ffffff"}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="center">
            <span><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title_url}">{$item.video_title|truncate:28}</a></span>
        </div>
    </div>
    {if $item.list_rank%$row == 0 || $item.list_rank == $info.total_items}
        </div>
    {/if}
{/foreach}

