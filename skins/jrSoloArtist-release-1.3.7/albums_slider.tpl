{capture name="row_template" assign="singles_slider_row"}
    {literal}
    {jrCore_module_url module="jrAudio" assign="murl"}
    <div class="table-div" style="margin:20px auto;">
        <div class="table-row-div">
            <div class="table-cell-div center middle p5" style="width:1%;">
                {if $info.total_pages > 1}
                    {if isset($info.prev_page) && $info.prev_page > 0}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrLoad('#albums_slider','{$info.page_base_url}/p={$info.prev_page}');">
                    {else}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button form_button_disabled">
                    {/if}
                {else}
                    <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button form_button_disabled">
                {/if}
            </div>
            <div class="table-cell-div" style="width:80%;">
                {if isset($_items)}
                {foreach from=$_items item="item"}
                <a onclick="jrLoad('#details','{$jamroom_url}/album_list/{$item.audio_album_url}');$('html, body').animate({ scrollTop: $('#detail').offset().top -100 }, 'slow');" title="{$item.audio_album}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="xxlarge" crop="auto" class="iloutline img_scale" alt=$item.audio_title style="max-width:72px;max-height:72px;"}</a>
                {/foreach}
                {/if}
            </div>
            <div class="table-cell-div center middle p5" style="width:1%;">
                {if $info.total_pages > 1}
                    {if isset($info.next_page) && $info.next_page > 1}
                        <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrLoad('#albums_slider','{$info.page_base_url}/p={$info.next_page}');">
                    {else}
                        <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button form_button_disabled">
                    {/if}
                {else}
                    <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button form_button_disabled">
                {/if}
            </div>
        </div>
    </div>
    {/literal}
{/capture}

{jrCore_list module="jrAudio" order_by="_created desc" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" group_by="audio_album" template=$singles_slider_row pagebreak="10" page=$_post.p}
