
{jrCore_lang module="jrFile" id="41" default="file" assign="page_title"}
{jrCore_module_url module="jrFile" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col8">
            <div  style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrCelebrity_sort template="icons.tpl" nav_mode="jrFile" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="file_title,file_type,"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrFile" order_by="_item_id numerical_desc" pagebreak=10 page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col4">
            <div class="box">
                {jrCelebrity_sort template="icons.tpl" nav_mode="jrFile" profile_url=$profile_url}
                <div class="box_body">
                    <div class="wrap">
                        <div id="chart">
                            {* jrCore_list module="jrFile" template="chart_file.tpl"  pagebreak=10 page=$_post.p pager=true *}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{jrCore_include template="footer.tpl"}
