{assign var="selected" value="music"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="13" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrLoad('#singles_slider',core_system_url +'/singles_slider');
        jrLoad('#albums_slider',core_system_url +'/albums_slider');
     });
</script>

{jrCore_module_url module="jrAudio" assign="murl"}
<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="block">
                <div class="title mb20">
                    <h1>Our Music</h1><br>
                </div>
                <div class="block_content center">

                    <div class="left middle" style="margin:0 auto;">
                        {jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="Singles"}:&nbsp;
                        {capture name="row_template" assign="single_add_row"}
                            {literal}
                                {if isset($_items)}
                                {jrCore_module_url module="jrAudio" assign="murl"}
                                {foreach from=$_items item="item"}
                                    <a onclick="jCore_window_location('{$jamroom_url}/{$item.profile_url}/{$murl}');">{jrCore_icon icon="plus"}</a>
                                {/foreach}
                                {/if}
                            {/literal}
                        {/capture}
                        {jrCore_list module="jrProfile" order_by="profile_name asc" limit="1" search="_profile_id = 1" template=$single_add_row}
                    </div>
                    <div id="singles_slider">
                    </div>

                    <hr>

                    <div class="left middle" style="margin:0 auto;">
                        {jrCore_lang skin=$_conf.jrCore_active_skin id="64" default="Albums"}:&nbsp;
                        {capture name="row_template" assign="single_add_row"}
                            {literal}
                                {if isset($_items)}
                                {jrCore_module_url module="jrAudio" assign="murl"}
                                {foreach from=$_items item="item"}
                                    <a onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$murl}/albums');">{jrCore_icon icon="plus"}</a>
                                {/foreach}
                                {/if}
                            {/literal}
                        {/capture}
                        {jrCore_list module="jrProfile" order_by="profile_name asc" limit="1" search="_profile_id = 1" template=$single_add_row}
                    </div>
                    <div id="albums_slider">
                    </div>

                    <hr>

                </div>

            </div>

            <a id="detail"></a>
            <div id="details">
                {if isset($_conf.jrSoloArtist_index_album) && strlen($_conf.jrSoloArtist_index_album) > 0}
                    {jrCore_list module="jrAudio" profile_id=$_conf.jrSoloArtist_main_id search2="audio_album = `$_conf.jrSoloArtist_index_album`" order_by="audio_file_track numerical_asc" template="album_row.tpl"}
                {else}
                    {if jrUser_is_master()}
                        <div class="center p20 middle">
                            {jrCore_lang skin=$_conf.jrCore_active_skin id="68" default="Change Album" assign="chng_abm_bttn_title"}
                            <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="62" default="Enter an Album name to show a Featured Album player here!"}</h3> &nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin=jrSoloArtist">{jrCore_image image="update.png" width="24" height="24" alt=$chng_abm_bttn_title title=$chng_abm_bttn_title}</a><br>
                        </div>
                    {/if}
                {/if}
            </div>

        </div>
    </div>

</div>

{jrCore_include template="footer.tpl"}

