{$page_template = "photoalbum"}
{jrCore_lang module="jrPhotoAlbum" id="11" default="photo album" assign="page_title"}
{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col12" style="padding: 0 1em">
            <h1 style="text-transform: capitalize">{$page_title}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col12">
            <div>
                <div class="box">
                    {jrISkin_sort template="icons.tpl" nav_mode="jrPhotoAlbum" profile_url=$profile_url}
                    {jrSearch_module_form module="jrPhotoAlbum" fields="photoalbum_title"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrPhotoAlbum" order_by="_created NUMERICAL_DESC" pagebreak="16" page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}
