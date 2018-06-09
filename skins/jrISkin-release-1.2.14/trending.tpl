<div class="box">
    {jrISkin_sort template="icons.tpl" nav_mode="trending" profile_url=$profile_url}
    <div class="box_body">

        <div class="wrap">
            <div class="media trending">
                <div class="wrap">
                    {$smarty.now|jrCore_date_format:"%A %B %d, %Y"}<br>
                    {jrAction_hash_list template="trending_hashtag.tpl" order_by="count" limit=12 days=15}
                </div>
            </div>
        </div>
    </div>
</div>