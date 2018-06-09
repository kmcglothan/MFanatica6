{jrCore_module_url module="jrTags" assign="murl"}
<div>

    {if $show_title === true}

    <div class="breadcrumbs">
        <a href="#">{jrCore_lang module="jrTags" id=7 default="Tagged"}</a> <a href="#">&quot;{$tag_text}&quot;</a> <a href="#">{$active_label}</a>
    </div>

        <div class="action_buttons">
            <div id="tag_cloud_button" class="block_config">
                <a href="{$jamroom_url}/{$murl}"><input type="button" value="{jrCore_lang module="jrTags" id=6 default="Tag Cloud"}" class="form_button"></a>
            </div>
        </div>
    {else}

        <div class="breadcrumbs">
            <a href="#">{$page_title} </a>
        </div>

    {/if}

</div>