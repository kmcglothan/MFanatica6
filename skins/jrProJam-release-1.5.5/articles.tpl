{assign var="selected" value="ban"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="articles" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
     });
</script>

<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1">

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="articles"}</h2>
                            <div class="body_3">
                                {jrCore_list module="jrPage" order_by="_created desc" tpl_dir="jrProJam" template="articles_row.tpl" pagebreak=$_conf.jrProJam_default_articles_pagebreak page=$_post.p}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1 ml5">
                {jrCore_include template="side_home.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
