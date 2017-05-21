{jrCore_module_url module="jrGallery" assign="murl"}
{if isset($_items)}
<div class="container">

    {foreach from=$_items item="item"}
    {if $item@first || ($item@iteration % 3) == 1}
    <div class="row">
    {/if}

        <div class="col4{if $item@last || ($item@iteration % 3) == 0} last{/if}">
            <div class="item jr_gallery_row left">
                <div class="center">
                    <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">{$item.gallery_title}</a></h2>
                </div>
                <br>
                <div class="p10 mt10" style="padding-top:0">
                    {jrCore_list module="jrGallery" search1="gallery_title_url = `$item.gallery_title_url`" template="null" order_by="gallery_order numerical_asc" return_keys="_item_id" limit="3" assign="image_keys"}
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">
                        {jrCore_module_function function="jrImage_stacked_image" module="jrGallery" type="gallery_image" item_id="`$image_keys[2]._item_id`,`$image_keys[1]._item_id`,`$image_keys[0]._item_id`" size="icon" alt="{$item.gallery_title}"}
                    </a>
                </div>
            </div>
        </div>
    {if $item@last || ($item@iteration % 3) == 0}
    </div>
    {/if}

    {/foreach}
    {if $info.total_pages > 1}
    <div class="row">
        <div class="col12 last">
            <div class="block">
                <table style="width:100%;">
                    <tr>

                        <td style="width:25%;">
                            {if isset($info.prev_page) && $info.prev_page > 0}
                                <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrCore_window_location('{$info.page_base_url}/p={$info.prev_page}');">
                            {/if}
                        </td>

                        <td style="width:50%;text-align:center;">
                            {if $info.total_pages <= 5 || $info.total_pages > 500}
                                {$info.page} &nbsp;/ {$info.total_pages}
                            {else}
                                <form name="form" method="post" action="_self">
                                    <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrCore_window_location('{$info.page_base_url}/p=' +sel);">
                                        {for $pages=1 to $info.total_pages}
                                            {if $info.page == $pages}
                                                <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                            {else}
                                                <option value="{$pages}"> {$pages}</option>
                                            {/if}
                                        {/for}
                                    </select>&nbsp;/&nbsp;{$info.total_pages}
                                </form>
                            {/if}
                        </td>

                        <td style="width:25%;text-align:right;">
                            {if isset($info.next_page) && $info.next_page > 1}
                                <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrCore_window_location('{$info.page_base_url}/p={$info.next_page}');">
                            {/if}
                        </td>

                    </tr>
                </table>
            </div>
        </div>
    </div>
    {/if}

</div>
{/if}
