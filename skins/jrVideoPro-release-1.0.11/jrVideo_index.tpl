{jrCore_lang module="jrVideo" id="39" default="Video" assign="page_title"}
{jrCore_module_url module="jrVideo" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="video_index">
    <div class="wrap">
        <div class="row">
            <div class="col6">
                <h1>{jrCore_lang module="jrVideo" id=39 default="Videos"}</h1>
            </div>

            <div class="col6">
                {jrSearch_module_form fields="video_title,video_description,video_category,video_album"} {jrCore_list module="jrVideo" group_by="video_category" order_by="video_category asc" limit=100 template="select_category.tpl"}
            </div>
        </div>
        <div class="row">
            {if strlen($_post.category) > 0}
                {jrCore_list module="jrVideo" search="video_category = `$_post.category`" order_by="_created numerical_desc" pagebreak=30 page=$_post.p pager=true template="index_item_5.tpl"}
            {elseif $_post.option == 'top-rated'}
                {jrCore_list module="jrVideo" order_by="video_rating_overall_average_count numerical_desc" pagebreak=30 page=$_post.p pager=true template="index_item_5.tpl"}
            {elseif $_post.option == 'most-watched'}
                {jrCore_list module="jrVideo" chart_days=$_conf.jrVideoPro_watched_days chart_field="video_file_stream_count" pagebreak=30 page=$_post.p pager=true template="index_item_5.tpl"}
            {elseif $_post.option == 'new-series'}
                {jrCore_list module="jrVideo" group_by="video_album" order_by="_created numerical_desc" pagebreak=30 page=$_post.p pager=true template="index_item_5.tpl"}
            {elseif $_post.option == 'staff-picks'}
                {if isset($_conf.jrVideoPro_staff_picks) && $_conf.jrVideoPro_staff_picks > 0}
                    {jrCore_list module="jrVideo" search="_item_id in `$_conf.jrVideoPro_staff_picks`" pagebreak=30 page=$_post.p pager=true template="index_item_5.tpl"}
                {else}
                    {jrCore_list module="jrVideo" pagebreak=30 page=$_post.p pager=true template="index_item_5.tpl"}
                {/if}
            {else}
                {jrCore_list module="jrVideo" order_by="_created numerical_desc" pagebreak=30 page=$_post.p pager=true template="index_item_5.tpl"}
            {/if}

        </div>
        <br>
    </div>
</div>

{jrCore_include template="footer.tpl"}

