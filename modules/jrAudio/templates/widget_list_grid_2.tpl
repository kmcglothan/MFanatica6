<div class="container">
    {if isset($_items)}
        {jrCore_module_url module="jrAudio" assign="murl"}
        {foreach from=$_items item="item"}

            {if $item@first || ($item@iteration % 2) == 1}
                <div class="row">
            {/if}
            <div class="col6{if $item@last || ($item@iteration % 2) == 0} last{/if}">
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">
                    {jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.audio_title width=false height=false}
                </a>
            </div>
            {if $item@last || ($item@iteration % 2) == 0}
                </div>
            {/if}

        {/foreach}
    {/if}
</div>
