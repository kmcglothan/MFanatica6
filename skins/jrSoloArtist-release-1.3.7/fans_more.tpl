{capture name="row_template" assign="more_fans_row"}
    {literal}
        <div class="block">

            <div class="title">
                <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="71" default="Our Fans"}&nbsp;<span class="normal right capital"><a href="{$jamroom_url}/fans">{jrCore_lang skin=$_conf.jrCore_active_skin id="75" default="Back"}&nbsp;&raquo;</a></span></h1><br>
            </div>
            <div class="block_content">
                <div class="container">
                    <div class="row">
                        {if isset($_items)}
                        {foreach from=$_items item="item"}
                        <div class="col3">
                            <div class="item">
                                <div class="p5 center">
                                    <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="medium" crop="auto" alt=$item.profile_name title=$item.profile_name class="iloutline img_shadow"}</a><br>
                                    <h3><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a></h3>
                                </div>
                            </div>
                        </div>
                        {/foreach}
                        {/if}
                    </div>
                </div>
                <hr>
            </div>
            {if $info.total_pages > 1}
            <div style="display:table;width:70%;margin:0 auto;">
                <div style="display:table-row;">
                    <div style="display:table-cell;width:1%;">
                        {if isset($info.prev_page) && $info.prev_page > 0}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrLoad('#fan_main','{$info.page_base_url}/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#fantop').offset().top -100 }, 'slow');">
                        {else}
                        <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button form_button_disabled">
                        {/if}
                    </div>
                    <div class="center normal" style="display:table-cell;">
                        {if $info.total_pages <= 5}
                        {$info.page} &nbsp;/ {$info.total_pages}
                        {else}
                        <form name="form" method="post" action="_self">
                            <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad=('#fan_main','{$info.page_base_url}/p=' +sel);$('html, body').animate({ scrollTop: $('#fantop').offset().top -100 }, 'slow');">
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
                    </div>
                    <div style="display:table-cell;width:1%;">
                        {if isset($info.next_page) && $info.next_page > 1}
                        <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrLoad('#fan_main','{$info.page_base_url}/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#fantop').offset().top -100 }, 'slow');">
                        {else}
                        <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button form_button_disabled">
                        {/if}
                    </div>
                </div>
            </div>
            {/if}
        </div>
    {/literal}
{/capture}

{jrCore_list module="jrProfile" order_by="_created desc" quota_id=$_conf.jrSoloArtist_fan_quota_id search1="profile_active = 1" template=$more_fans_row require_image="profile_image" pagebreak="16" page=$_post.p }
