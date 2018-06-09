{jrCore_module_url module="jrCombinedAudio" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="row" style="overflow: visible">
        <div class="col8">
            <div class="breadcrumbs">
                {jrCore_include template="profile_header_minimal.tpl"}
                {jrCelebrity_breadcrumbs module="jrAudio" profile_url=$profile_url profile_name=$profile_name page="index"}
            </div>
        </div>
        <div class="col4">
            <div class="action_buttons">
                {jrCore_item_index_buttons module="jrCombinedAudio" profile_id=$_profile_id}
            </div>
        </div>
    </div>
</div>

<div class="col8">
    <div class="box">

        {jrCelebrity_sort template="icons.tpl" nav_mode="jrAudio" profile_url=$profile_url}

        <span>{jrCore_lang module="jrAudio" id="41" default="Audio"} by {$profile_name}</span>

        <div class="box_body">
            <div class="wrap">
                <div id="list" class="main">
                    {jrCombinedAudio_get_active_modules assign="mods"}
                    {if strlen($mods) > 0}
                        {jrSeamless_list modules=$mods search="_profile_id = `$_profile_id`" order_by="*_display_order numerical_asc" pagebreak=8 page=$_post.p pager=true}
                    {elseif jrUser_is_admin()}
                        No active audio modules found!
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col4 last">
    <div class="box">

        {jrCelebrity_sort template="icons.tpl" nav_mode="jrAudio" profile_url=$profile_url}

        <span>{jrCore_lang skin="jrCelebrity" id="111" default="You May Also Like"}</span>

        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCombinedAudio_get_active_modules assign="mods"}
                    {if strlen($mods) > 0}
                        {jrSeamless_list modules=$mods search="_profile_id != `$_profile_id`" order_by='_item_id random' limit=12 template="chart_audio.tpl"}
                    {elseif jrUser_is_admin()}
                        No active audio modules found!
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>



