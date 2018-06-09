{jrCore_lang module="jrPlaylist" id="9" default="Playlist" assign="page_title"}
{jrCore_module_url module="jrPlaylist" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col12">
            <div>
                <div class="box">
                    {jrCelebrity_sort template="icons.tpl" nav_mode="jrPlaylist" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="playlist_title"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrPlaylist" order_by="_created NUMERICAL_DESC" pagebreak="10" page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}