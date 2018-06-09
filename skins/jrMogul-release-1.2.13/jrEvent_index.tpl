{jrCore_lang module="jrEvent" id=23 default="Events" assign="page_title"}
{jrCore_module_url module="jrEvent" assign="murl"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="fs">
    <div class="row">
        <div class="col8">
            <div  style="padding: 0 1em 0 0;">
                <div class="box">
                    {jrMogul_sort template="icons.tpl" nav_mode="jrEvent" profile_url=$profile_url}
                    <span>{$page_title}</span>
                    {jrSearch_module_form fields="event_title,event_description"}
                    <div class="box_body">
                        <div class="wrap">
                            <div id="list">
                                {if isset($ts_start) && strlen($ts_start) > 0 && (isset($ts_end) && strlen($ts_end) > 0)}
                                    {jrCore_list module="jrEvent" search1="event_date >= $ts_start" search2="event_date <= $ts_end" order_by="event_date asc" pagebreak=10 page=$_post.p pager=true}
                                {else}
                                    {jrCore_list module="jrEvent" order_by="event_date asc" pagebreak=10 page=$_post.p pager=true}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col4">
            <div class="box">
                {jrMogul_sort template="icons.tpl" nav_mode="jrDocs" profile_url=$profile_url}
                <span>{jrCore_lang skin="jrMogul " id=31 default="Most Popular"}</span>
                <div class="box_body">
                    <div class="wrap">
                        <div id="chart">
                            {jrCore_list module="jrEvent" order_by="event_like_count numerical_desc" template="chart_event.tpl"  pagebreak=8 page=$_post.p pager=true}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl"}