{jrCore_module_url module="jrCombinedAudio" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrCombinedAudio" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</h1><br>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</a>
        </div>
    </div>

    <div class="block_content">
        {jrCombinedAudio_get_active_modules assign="mods"}
        {if strlen($mods) > 0}
            {jrSeamless_list modules=$mods search="_profile_id = `$_profile_id`" order_by="*_display_order numerical_asc" pagebreak=6 page=$_post.p pager=true}
        {elseif jrUser_is_admin()}
            No active audio modules found!
        {/if}
    </div>

</div>
