{jrCore_lang module="jrGallery" id=38 default="Images" assign="page_title"}
{jrCore_module_url module="jrGallery" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col12">
            <div>
                <div class="box">
                    {jrMaestro_sort template="icons.tpl" nav_mode="jrGallery" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form module="jrGallery" fields="gallery_title,gallery_image_title,gallery_caption,gallery_description,gallery_image_name"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {if isset($_post.ss)}
                                    {jrCore_list module="jrGallery" profile_id=$_profile_id order_by="_item_id desc" require_image="gallery_image" pagebreak=12 page=$_post.p pager=true}
                                {else}
                                    {jrCore_list module="jrGallery" profile_id=$_profile_id order_by="_item_id desc" group_by="gallery_title_url" require_image="gallery_image" pagebreak=6 page=$_post.p pager=true}
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
