{assign var="selected" value="articles"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="53" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="inner leader mb8">
    <span class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="53" default="Article"}</span>
</div>
<div class="inner">

{jrCore_list module="jrPage" profile_id="0" search="_item_id = `$_post.option`" order_by="_created desc" template="article_row.tpl"}

</div>

{jrCore_include template="footer.tpl"}