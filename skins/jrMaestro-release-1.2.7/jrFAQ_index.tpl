{jrCore_lang module="jrFAQ" id=10 default="FAQs" assign="page_title"}
{jrCore_module_url module="jrFAQ" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col12">
            <div  style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrMaestro_sort template="icons.tpl" nav_mode="jrFAQ" profile_url=$profile_url}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrFAQ" order_by="_created numerical_asc" group_by="faq_category" pagebreak="10" page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}