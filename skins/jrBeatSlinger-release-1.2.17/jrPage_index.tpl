{jrCore_lang module="jrPage" id="19" default="Pages" assign="page_title"}
{jrCore_module_url module="jrPage" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}



<div class="fs">
    <div class="row">
        <div class="col12">
            <div>
                <div class="box">
                    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrPage" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="page_title,page_body"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrPage" search="page_location = 0" order_by="_created desc" pagebreak="12" page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}
