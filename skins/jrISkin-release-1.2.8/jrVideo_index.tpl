{jrCore_lang module="jrVideo" id="39" default="Video" assign="page_title"}
{jrCore_module_url module="jrVideo" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}


<div class="fs">
    <div class="row">
        <div class="col8">
            <div  style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrISkin_sort template="icons.tpl" nav_mode="jrVideo" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="video_title,video_album,video_genre"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {jrCore_list module="jrVideo" order_by="_item_id numerical_desc" pagebreak=10 page=$_post.p pager=true}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col4">
            <div class="box">
                {jrISkin_sort template="icons.tpl" nav_mode="jrVideo" profile_url=$profile_url}
                <span>{jrCore_lang skin="jrISkin" id=32 default="30 Day Charts"}</span>
                <div class="box_body">
                    <div class="wrap">
                        <div id="list">
                            {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="30" template="chart_video.tpl"  pagebreak=10 page=$_post.p pager=true}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}
