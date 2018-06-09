{* We're showing a list of existing albums *}

<div class="block">
    <div style="float:right;">
        {jrCore_item_create_button module="jrAudio" profile_id=$_profile_id action="audio/create_album" image="create_album.png" alt="35"}
        {jrCore_item_create_button module="jrAudio" profile_id=$_profile_id}
    </div>
    <h1>{jrCore_lang module="jrAudio" id="34" default="Albums"}</h1>
    <div class="breadcrumbs">
        <a href="{$jamroom_url}"><span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="home"}</span></a> &raquo; <a href="{$jamroom_url}/music">{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="Music"}</a> &raquo; {jrCore_lang module="jrAudio" id="34" default="Albums"}
    </div>
</div>

{capture name="row_template" assign="template"}
    {literal}
        {if isset($_items) && is_array($_items)}
            {jrCore_module_url module="jrAudio" assign="murl"}
            <div class="item">
                <div class="container">
                    <div class="row">

                    {foreach from=$_items item="item"}
                        <div class="col3{if $item@last} last{/if}">
                            <div class="center p5">
                                <a href="#detail" onclick="jrLoad('#details','{$jamroom_url}/album_list/{$item.audio_album_url}');">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.audio_title title=$item.audio_title class="iloutline"}</a><br>
                                <a href="#detail" onclick="jrLoad('#details','{$jamroom_url}/album_list/{$item.audio_album_url}');"><span class="capital bold">{$item.audio_album}</span></a>
                            </div>
                        </div>
                    {/foreach}

                    </div>
                </div>
            </div>
            {if $info.total_pages > 1}
                <div class="table-div" style="width:100%;">
                    <div class="table-row-div">
                        <div class="table-cell-div right p5 middle" style="width:10%;">
                            {if isset($info.prev_page) && $info.prev_page > 0}
                                <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrLoad('#details','{$info.page_base_url}/p={$info.prev_page}');">
                            {else}
                                <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button form_button_disabled">
                            {/if}
                        </div>
                        <div class="table-cell-div center p5 middle" style="width:80%;"></div>
                        <div class="table-cell-div left p5 middle" style="width:10%;">
                            {if isset($info.next_page) && $info.next_page > 1}
                                <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrLoad('#details','{$info.page_base_url}/p={$info.next_page}');">
                            {else}
                                <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button form_button_disabled">
                            {/if}
                        </div>
                    </div>
                </div>
            {/if}
        {/if}
    {/literal}
{/capture}

{jrCore_list module="jrAudio" profile_id=$_conf.jrSoloArtist_main_id order_by="_created desc" group_by="audio_album_url" pagebreak="6" page=$_post.p template=$template}

