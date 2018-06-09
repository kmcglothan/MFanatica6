{* default index for profile *}
{jrCore_module_url module="jrFAQ" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrFAQ" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrFAQ" profile_id=$_profile_id}
    </div>
</div>


<div class="box">
    {jrCelebrity_sort template="icons.tpl" nav_mode="jrFAQ" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div id="list" class="faq">
                {jrCore_list module="jrFAQ" profile_id=$_profile_id order_by="faq_display_order numerical_asc" group_by="faq_category_url" pagebreak="10" page=$_post.p pager=true}
            </div>
        </div>
    </div>
</div>

