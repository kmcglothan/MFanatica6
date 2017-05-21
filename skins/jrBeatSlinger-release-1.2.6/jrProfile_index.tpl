{jrCore_lang module="jrProfile" id="26" default="Profiles" assign="page_title"}
{jrCore_module_url module="jrProfile" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col8">
            <div  style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrProfile" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="profile_namee,profile_bio,profile_genre"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrProfile" order_by="_item_id numerical_desc" pagebreak=10 page=$_post.p pager=true require_imge="profile_image"}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col4">
            <div class="box">
                {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrProfile" profile_url=$profile_url}
                <span>{jrCore_lang skin="jrBeatSlinger" id=31 default="Charts"}</span>
                <div class="box_body">
                    <div class="wrap">
                        <div id="chart">
                            {jrCore_list module="jrProfile" order_by="profile_like_count numerical_desc" template="chart_profile.tpl"  pagebreak=8 page=$_post.p pager=true require_imge="profile_image"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}