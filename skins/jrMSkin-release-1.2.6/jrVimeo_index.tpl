{jrCore_lang module="jrVimeo" id=38 default="Vimeo" assign="page_title"}
{jrCore_module_url module="jrVimeo" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col8">
            <div style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrMSkin_sort template="icons.tpl" nav_mode="jrVimeo" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="vimeo_title,vimeo_album,vimeo_genre"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrVimeo" order_by="_item_id numerical_desc" pagebreak=10 page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col4">
            <div class="box">
                {jrMSkin_sort template="icons.tpl" nav_mode="jrVimeo" profile_url=$profile_url}
                <span>{jrCore_lang skin="jrMSkin" id=31 default="Most Popular"}</span>

                <div class="box_body">
                    <div class="wrap">
                        <div id="list">
                            {jrCore_list module="jrVimeo" order_by="vimeo_like_count numerical_desc" template="chart_vimeo.tpl"  pagebreak=10 page=$_post.p pager=true}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{jrCore_include template="footer.tpl"}
