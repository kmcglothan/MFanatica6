{capture name="row_template" assign="newest_fans_row"}
    {literal}
    <div class="table-div" style="margin:20px auto;">
        <div class="table-row-div">
            <div class="table-cell-div center middle p5" style="width:1%;">
                {if $info.total_pages > 1}
                    {if isset($info.prev_page) && $info.prev_page > 0}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrLoad('#new_fans','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#fantop').offset().top -100 }, 'slow');">
                    {else}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button form_button_disabled">
                    {/if}
                {else}
                    <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button form_button_disabled">
                {/if}
            </div>
            <div class="table-cell-div center" style="width:80%;">
                {if isset($_items)}
                <div class="item">
                {foreach from=$_items item="item"}
                    <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="icon" crop="auto" alt=$item.profile_name title=$item.profile_name class="iloutline img_shadow"}</a>
                {/foreach}
                </div>
                {/if}
            </div>
            <div class="table-cell-div center middle p5" style="width:1%;">
                {if $info.total_pages > 1}
                    {if isset($info.next_page) && $info.next_page > 1}
                        <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrLoad('#new_fans','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#fantop').offset().top -100 }, 'slow');">
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

{jrCore_list module="jrProfile" order_by="_created desc" quota_id=$_conf.jrSoloArtist_fan_quota_id search1="profile_active = 1" template=$newest_fans_row require_image="profile_image" pagebreak="6" page=$_post.p }
