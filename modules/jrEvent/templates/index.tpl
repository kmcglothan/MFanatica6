{jrCore_include template="header.tpl"}

<div class="block">

    <div class="title">
        {jrSearch_module_form fields="event_title,event_description"}
        <h1>{jrCore_lang module="jrEvent" id="23" default="Events"}</h1>
    </div>

    <div class="block_content">

        {if isset($ts_start) && strlen($ts_start) > 0 && (isset($ts_end) && strlen($ts_end) > 0)}
            {jrCore_list module="jrEvent" search1="event_date >= $ts_start" search2="event_date <= $ts_end" order_by="event_date asc" pagebreak=10 page=$_post.p pager=true}
        {else}
            {jrCore_list module="jrEvent" order_by="event_date asc" pagebreak=10 page=$_post.p pager=true}
        {/if}

    </div>

</div>

{jrCore_include template="footer.tpl"}