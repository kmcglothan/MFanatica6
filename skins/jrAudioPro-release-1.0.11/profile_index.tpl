{* default index for profile *}

<div class="col8 {$last}">

    <div id="list" style="padding: 1.5em 1em 0">
        <div class="head">{jrCore_icon icon="audio" size="20" color="ff5500"} Tracks</div>
        {if jrCore_module_is_active('jrAudio')}
            {jrCore_list module="jrAudio" profile_id=$_profile_id order_by='audio_display_order numerical_asc' limit="10" assign="audio_list"}
        {/if}

        {if strlen($audio_list) > 3}
            {$audio_list}
        {else}
            <div class="no-items">
                {jrCore_lang skin=$_conf.jrCore_active_skin id="62" default="No items found"}
            </div>
        {/if}



    </div>
</div>

{jrCore_include template="profile_sidebar.tpl"}