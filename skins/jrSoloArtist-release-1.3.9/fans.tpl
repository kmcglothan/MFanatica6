{assign var="selected" value="fans"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="72" default="Fans" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrLoad('#fan_activity',core_system_url +'/fan_activity');
        jrLoad('#new_fans',core_system_url +'/fans_list');
         });
</script>

<a id="fantop" name="fantop"></a>
<div id="fan_main">

    <div class="container">

        <div class="row">

            <div class="col12">

                <div class="block">

                    <div class="title">
                        <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="Newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="72" default="Fans"}</h1>
                    </div>
                    <div class="block_content">
                        <div id="new_fans">
                        </div>
                    </div>

                    <hr>
                    <div class="normal right capital" style="padding-right:30px;"><a onclick="jrLoad('#fan_main','{$jamroom_url}/fans_more');$('html, body').animate({ scrollTop: $('#fantop').offset().top -100 }, 'slow');">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="more"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="72" default="fans"}&nbsp;&raquo;</a></div>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col4">
                <div class="block">
                    <div class="title">
                        <h1>Fan Activity</h1><br>
                    </div>
                    <div class="block_content">
                        <div id="fan_activity">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col8 last">

                <div class="block">

                    <div class="title">
                        <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="Latest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="53" default="Comments"}</h1><br>
                    </div>
                    <div class="block_content">
                        {if jrCore_module_is_active('jrComment')}
                            {jrCore_list module="jrComment" order_by="_created NUMERICAL_DESC" template="fan_comments.tpl" limit="5"}
                        {/if}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{jrCore_include template="footer.tpl"}