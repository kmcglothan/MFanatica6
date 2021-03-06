{assign var="selected" value="galleries"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="29" default="galleries" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#alpha_galleries',core_system_url + '/gallery_list/alpha');
        jrLoad('#new_galleries',core_system_url + '/gallery_list/newest');
        jrLoad('#top_galleries',core_system_url + '/gallery_list/top');
     });
</script>

<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1">
                <div class="container">

                    <a id="tgalleries" name="tgalleries"></a>
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_3 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="Top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="29" default="galleries"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#top_galleries','{$jamroom_url}/gallery_list/top');$('html, body').animate({ scrollTop: $('#tgalleries').offset().top -100 }, 'slow');">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="top_galleries">
                                </div>
                            </div>
                        </div>
                    </div>

                    <a id="ngalleries" name="ngalleries"></a>
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_3 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#new_galleries','{$jamroom_url}/gallery_list/newest');$('html, body').animate({ scrollTop: $('#ngalleries').offset().top -100 }, 'slow');">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="new_galleries">
                                </div>
                            </div>
                        </div>
                    </div>

                    <a id="agalleries" name="agalleries"></a>
                    <div class="row">
                        <div class="col12 last">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="29" default="galleries"}</h2>
                            <div class="body_3 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#alpha_galleries','{$jamroom_url}/gallery_list/alpha');$('html, body').animate({ scrollTop: $('#agalleries').offset().top -100 }, 'slow');">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="alpha_galleries">
                                </div>
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
