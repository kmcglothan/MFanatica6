{assign var="selected" value="articles"}
{assign var="spotlight" value="yes"}
{assign var="no_inner_div" value="true"}
{jrCore_lang  skin=$_conf.jrCore_active_skin id="103" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

<div class="inner">
    <div class="inner leader mb8">
        <span class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="53" default="Article"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="52" default="Archives"}</span>
    </div>

{jrCore_list module="jrPage" search="page_location = 0" order_by="_created desc" tpl_dir="jrNova" template="index_content.tpl" pagebreak=$_conf.jrNova_default_pagebreak page=$_post.p}

</div>

{jrCore_include template="footer.tpl"}