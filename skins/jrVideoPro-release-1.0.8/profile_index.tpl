{* default index for profile *}

<div class="col8 {$last}">
    <div id="list" style="padding: 1em 2em">
        <div class="head">
            {jrCore_icon icon="video" size="20" color="D2B48C"}
            {jrCore_lang module="jrVideo" id=39 default="Videos"}
        </div>
        {if jrCore_module_is_active('jrVideo')}
            {jrCore_list module="jrVideo" profile_id=$_profile_id order_by='video_display_order numerical_asc' limit="10" assign="video_list"}
        {/if}

        {if strlen($video_list) > 3}
            {$video_list}
        {else}
            <div class="no-items">
                {jrCore_lang skin=$_conf.jrCore_active_skin id=69 default="No items found"}
            </div>
        {/if}


    </div>
</div>

{jrCore_include template="profile_sidebar.tpl"}