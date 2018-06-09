{* See if our blog module is enabled *}
{jrCore_module_url module="jrBlog" assign="murl"}
<a href="{$jamroom_url}/{$murl}/feed/1" target="_blank">{jrCore_icon icon="rss"}&nbsp;<span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="135" default="Site Blogs"}&nbsp;-&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="133" default="Subscribe"}</span></a><br>
