{jrCore_module_url module="jrCombinedVideo" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrCombinedVideo" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrCombinedVideo" id=1 default="Videos"}</h1><br>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrCombinedVideo" id=1 default="Videos"}</a>
        </div>
    </div>

    <div class="block_content">
        {jrCombinedVideo_get_active_modules assign="mods"}
        {if strlen($mods) > 0}
            {jrSeamless_list modules=$mods search="_profile_id = `$_profile_id`" order_by="*_display_order numerical_asc" pagebreak=6 page=$_post.p pager=true}
        {elseif jrUser_is_admin()}
            No active video modules found!
        {/if}
    </div>

</div>
