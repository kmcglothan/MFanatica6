{assign var="selected" value="youtube"}
{assign var="no_inner_div" value="true"}
{jrCore_lang  skin=$_conf.jrCore_active_skin id="15" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

{if isset($_post.option) && strlen($_post.option) > 0}

    {assign var="alphaclass" value="p_choice"}
    {if $_post.option == 'by_newest'}
        {assign var="newclass" value="p_choice_active"}
        {assign var="order_by" value="_created numerical_desc"}
        {assign var="rateclass" value="p_choice"}
        {assign var="playsclass" value="p_choice"}
    {elseif $_post.option == 'by_ratings'}
        {assign var="newclass" value="p_choice"}
        {assign var="rateclass" value="p_choice_active"}
        {assign var="order_by" value="youtube_rating_1_average_count NUMERICAL_DESC"}
        {assign var="playsclass" value="p_choice"}
    {elseif $_post.option == 'by_plays'}
        {assign var="newclass" value="p_choice"}
        {assign var="rateclass" value="p_choice"}
        {assign var="playsclass" value="p_choice_active"}
        {assign var="order_by" value="youtube_stream_count NUMERICAL_DESC"}
    {/if}

{else}

    {assign var="alphaclass" value="p_choice_active"}
    {assign var="order_by" value="youtube_title asc"}
    {assign var="newclass" value="p_choice"}
    {assign var="rateclass" value="p_choice"}
    {assign var="playsclass" value="p_choice"}

{/if}

<div class="menu_tab">
    <div class="{$alphaclass}" onclick="jrCore_window_location('{$jamroom_url}/you_tube');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="15" default="YouTube"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="48" default="by name"}</div>
    <div class="{$newclass}" onclick="jrCore_window_location('{$jamroom_url}/you_tube/by_newest');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="15" default="YouTube"}</div>
    <div class="{$rateclass}" onclick="jrCore_window_location('{$jamroom_url}/you_tube/by_ratings');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="15" default="YouTube"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="54" default="by ratings"}</div>
    <div class="{$playsclass}" onclick="jrCore_window_location('{$jamroom_url}/you_tube/by_plays');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="15" default="YouTube"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="50" default="by plays"}</div>
    <div class="clear"></div>
</div>
<div class="inner">

{jrCore_list module="jrYouTube" order_by=$order_by template="you_tube_row.tpl" pagebreak=$_conf.jrNovaLight_default_pagebreak page=$_post.p}

</div>

{jrCore_include template="footer.tpl"}
