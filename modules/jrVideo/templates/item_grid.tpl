{jrCore_module_url module="jrVideo" assign="murl"}

{if $_params.grid == 2 ||$_params.grid == 3 ||$_params.grid == 4 ||$_params.grid == 6}
    {$col = 12/$_params.grid}
    {$grid = $_params.grid}
{else}
    {$col = 4}
    {$grid = 3}
{/if}
{if isset($_items)}
    {foreach $_items as $item}

        {if $item@first || ($item@iteration % $grid) == 1}
            <div class="row">
        {/if}
        <div class="col{$col}">
            <div class="item">
                <div class="container">
                    <div class="row">
                        <div class="col2">
                            <div class="block_image">
                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.video_title width=false height=false}</a>
                            </div>
                        </div>
                        <div class="col5">
                            <div class="p5">
                                <h3>
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{$item.video_title}</a>
                                </h3><br>
                                <span class="info">{jrCore_lang module="jrVideo" id="31" default="album"}:</span>
                                <span class="info_c"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{$item.video_album}</a></span><br>
                                {if isset({$item.video_category}) && strlen({$item.video_category}) > 0}
                                    <span class="info">{jrCore_lang module="jrVideo" id="12" default="category"}:</span>
                                    <span class="info_c">{$item.video_category}</span>
                                    <br>
                                {/if}
                                {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$item._item_id current=$item.video_rating_1_average_count|default:0 votes=$item.video_rating_1_count|default:0}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        {if $item@last || ($item@iteration % $grid) == 0}
            </div>
        {/if}


    {/foreach}
{/if}