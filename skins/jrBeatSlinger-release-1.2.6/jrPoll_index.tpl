{$page_template = "poll"}
{jrCore_lang module="jrPoll" id="1" default="Poll" assign="page_title"}
{jrCore_module_url module="jrPoll" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}


<div class="fs">
    <div class="row">
        <div class="col8">
            <div  style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrPoll" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="poll_title,poll_description"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrPoll" profile_id=$_profile_id order_by="_item_id numerical_desc" pagebreak="10" page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col4">
            <div class="box">
                {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrPoll" profile_url=$profile_url}
                <span>{jrCore_lang skin="jrBeatSlinger" id=31 default="Charts"}</span>
                <div class="box_body">
                    <div class="wrap">
                        <div id="list">
                            {jrCore_list module="jrPoll" profile_id=$_profile_id order_by="poll_like_count numerical_desc" pagebreak="10" page=$_post.p pager=true}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}
