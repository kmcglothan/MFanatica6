{assign var="selected" value="lists"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="members" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#alpha_members',core_system_url + '/members_list/alpha');
        jrLoad('#new_members',core_system_url + '/members_list/newest');
        jrLoad('#most_viewed_members',core_system_url + '/members_list/most_viewed');
     });
</script>
{if isset($_post.list) && $_post.list == 'new'}
    {assign var="order_by" value="_created desc"}
{else}
    {assign var="order_by" value="profile_name asc"}
{/if}


<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1">

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="members"}</h2>

                            <a id="mvmembers" name="mvmembers"></a>
                            <div class="body_3 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="46" default="most"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="47" default="viewed"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#most_viewed_members','{$jamroom_url}/members_list/most_viewed');$('html, body').animate({ scrollTop: $('#mvmembers').offset().top -100 }, 'slow');">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="most_viewed_members">
                                </div>
                            </div>

                            <a id="nmembers" name="nmembers"></a>
                            <div class="body_3 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#new_members','{$jamroom_url}/members_list/newest');$('html, body').animate({ scrollTop: $('#nmembers').offset().top -100 }, 'slow');">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="new_members">
                                </div>
                            </div>

                            <a id="amembers" name="amembers"></a>
                            <div class="body_3 mb20">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}&nbsp;&raquo;&nbsp;<a onclick="jrLoad('#alpha_members','{$jamroom_url}/members_list/alpha');$('html, body').animate({ scrollTop: $('#amembers').offset().top -100 }, 'slow');">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                                <div id="alpha_members">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1 ml5">
                {jrCore_include template="side_members.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
