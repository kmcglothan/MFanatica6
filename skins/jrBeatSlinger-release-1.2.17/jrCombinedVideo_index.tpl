{jrCore_lang module="jrCombinedVideo" id="1" default="Video" assign="page_title"}
{jrCore_module_url module="jrCombinedVideo" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col12">
            <div  style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrVideo" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="video_title,video_album,video_genre"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCombinedVideo_get_active_modules assign="mods"}
                                {if strlen($mods) > 0}
                                    {jrSeamless_list modules=$mods order_by="_created numerical_desc" pagebreak=10 page=$_post.p pager=true}
                                {elseif jrUser_is_admin()}
                                    No active video modules found!
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}
