<section class="white core">
    <div class="row">
        <div class="col6">
            <div class="head">
                <h1>{jrCore_lang skin="jrElastic2" id=42 default="Admin Blog"}</h1>
            </div>
            <div id="list">
                {jrCore_list module="jrBlog" profile_id=$_conf.jrElastic2_profile_ids order_by="_item_id desc" limit=1 template="index_core_blog_list.tpl"}
            </div>
            <div class="head">
                <h1>{jrCore_lang skin="jrElastic2" id=6 default="Forum Topics"}</h1>
            </div>
            <div class="list" style="padding: 20px 20px 0 0;">
                {jrCore_list module="jrForum" search="forum_post_count > 0" order_by="_item_id desc" limit=5 template="index_core_forum_list.tpl" quota_check=false}
            </div>
        </div>
        <div class="col6">
            <div class="head">
                <h1>{jrCore_lang skin="jrElastic2" id=8 default="New Members"}</h1>
            </div>
            <div class="list clearfix" style="padding: 0">
                {$limit = 20}
                {if jrCore_is_mobile_device()}
                    {$limit = 12}
                {/if}
                {jrCore_list module="jrProfile" order_by="_item_id desc" limit=$limit require_image="profile_image" template="index_core_profile_list.tpl"}
            </div>
        </div>
    </div>
</section>